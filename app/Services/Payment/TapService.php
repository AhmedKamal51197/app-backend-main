<?php

namespace App\Services\Payment;

use App\Enums\PaymentGatewaysEnum;
use App\Models\Order;
use App\Models\PaymentGateway\PaymentGatewayCheckout;
use App\Models\PaymentGateway\PaymentGatewayTransaction;
use App\Models\Project;
use App\Models\Refund;
use App\Models\ServicePackage;
use App\Models\User;
use App\Services\Order\OrderService;
use App\Services\Order\RenewOrderService;
use App\Services\Payment\Tap\TapClient;
use App\Services\Payment\Tap\TapException;
use App\Services\Payment\Tap\TapUnknownReferenceException;
use App\Support\Money;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * The service for Tap payments
 *
 * A checkout is only ever marked as paid from a charge fetched from Tap's API
 * with the secret key, never from the browser redirect or a webhook body alone.
 */
class TapService
{
    public const REFUND_PENDING = 'pending';
    public const REFUND_REFUNDED = 'refunded';
    public const REFUND_FAILED = 'failed';
    public const REFUND_FLAGGED = 'flagged';

    private const CHARGE_CAPTURED = 'CAPTURED';

    private const REFUND_FAILURES = ['CANCELLED', 'DECLINED', 'FAILED', 'REJECTED'];

    /**
     * Load the Tap client
     *
     * @param TapClient $client
     */
    public function __construct(protected TapClient $client)
    {
    }

    /**
     * Create a Tap charge for the checkout
     *
     * @param User $user
     * @param PaymentGatewayCheckout $checkout
     *
     * @return array Same shape as the MyFatoorah checkout so the frontend does not change
     *
     * @throws TapException
     */
    public function checkout(User $user, PaymentGatewayCheckout $checkout): array
    {
        $currency = $this->currency();
        $amountMinor = Money::toMinor($checkout->getAttribute('amount'), $currency);

        $payload = [
            'amount' => Money::toFloat($amountMinor, $currency),
            'currency' => $currency,
            'threeDSecure' => true,
            'save_card' => false,
            'customer_initiated' => true,
            'description' => 'Moawen checkout '.$checkout->getAttribute('uuid'),
            'metadata' => ['checkout_uuid' => $checkout->getAttribute('uuid')],
            'reference' => [
                'transaction' => $checkout->getAttribute('uuid'),
                'order' => $checkout->getAttribute('uuid'),
                'idempotent' => $checkout->getAttribute('uuid'),
            ],
            'customer' => $this->customer($user),
            'source' => ['id' => 'src_all'],
            'post' => ['url' => $this->webhookUrl()],
            'redirect' => ['url' => $this->returnUrl()],
        ];

        if ($merchantId = config('tap.merchant_id')) {
            $payload['merchant'] = ['id' => $merchantId];
        }

        $charge = $this->client->createCharge($payload);
        $url = data_get($charge, 'transaction.url');

        if (empty($charge['id']) || ! is_string($url) || $url === '') {
            throw new TapException('Tap did not return a payment page URL.');
        }

        $checkout->update([
            'gateway_reference' => $charge['id'],
            'gateway_status' => strtoupper((string) ($charge['status'] ?? 'INITIATED')),
            'currency' => $currency,
        ]);

        return [
            'status' => true,
            'id' => $charge['id'],
            'reference' => $checkout->getAttribute('uuid'),
            'url' => $url,
            // The mobile app reads the payment page from `payment_url`.
            'payment_url' => $url,
        ];
    }

    /**
     * Fetch the charge from Tap and fulfil the checkout exactly once
     *
     * @param string $chargeId
     *
     * @return PaymentGatewayCheckout
     *
     * @throws TapException
     */
    public function syncCharge(string $chargeId): PaymentGatewayCheckout
    {
        $charge = $this->client->retrieveCharge($chargeId);

        return DB::transaction(function () use ($charge, $chargeId) {
            // The row lock serialises the webhook, the redirect and the sync command.
            $checkout = PaymentGatewayCheckout::query()
                ->where('payment_gateway', PaymentGatewaysEnum::TAP->value)
                ->where('gateway_reference', $charge['id'] ?? $chargeId)
                ->lockForUpdate()
                ->first();

            if (! $checkout) {
                throw new TapUnknownReferenceException("Unknown Tap charge [{$chargeId}].");
            }

            $status = strtoupper((string) ($charge['status'] ?? 'UNKNOWN'));

            if ($checkout->getAttribute('is_processed')) {
                return $checkout;
            }

            $checkout->setAttribute('gateway_status', $status);

            if ($status === self::CHARGE_CAPTURED) {
                if (! $this->chargeMatchesCheckout($charge, $checkout)) {
                    $checkout->setAttribute('gateway_status', 'AMOUNT_MISMATCH');
                    $checkout->save();

                    Log::critical('Tap capture does not match the checkout', [
                        'checkout' => $checkout->getAttribute('uuid'),
                        'charge' => $charge['id'] ?? null,
                        'expected' => [$checkout->getAttribute('amount'), $this->checkoutCurrency($checkout)],
                        'received' => [$charge['amount'] ?? null, $charge['currency'] ?? null],
                    ]);

                    return $checkout;
                }

                PaymentGatewayTransaction::create([
                    'transaction_id' => $charge['id'],
                    'transaction_response' => $charge,
                    'amount' => $checkout->getAttribute('amount'),
                    'payment_gateway' => PaymentGatewaysEnum::TAP->value,
                ]);

                $this->fulfil($checkout);

                $checkout->setAttribute('is_processed', true);
                $checkout->setAttribute('paid_at', now());
            }

            $checkout->save();

            return $checkout;
        });
    }

    /**
     * Refund a paid Tap checkout to the customer's card. A null amount refunds the remaining balance.
     *
     * @param PaymentGatewayCheckout $checkout
     * @param float|null $amount
     * @param string $reason
     *
     * @return Refund
     *
     * @throws TapException
     */
    public function refund(PaymentGatewayCheckout $checkout, ?float $amount = null, string $reason = 'requested_by_customer'): Refund
    {
        $refund = DB::transaction(function () use ($checkout, $amount) {
            $checkout = PaymentGatewayCheckout::whereKey($checkout->getKey())->lockForUpdate()->firstOrFail();

            if ($checkout->getAttribute('payment_gateway') !== PaymentGatewaysEnum::TAP->value
                || ! $checkout->getAttribute('is_processed')
                || ! $checkout->getAttribute('gateway_reference')) {
                throw new TapException(__('Only paid Tap checkouts can be refunded'));
            }

            $currency = $this->checkoutCurrency($checkout);

            // Pending refunds reserve their amount so two refunds can never exceed the charge.
            $reservedMinor = $this->refundsOf($checkout)
                ->whereIn('status', [self::REFUND_PENDING, self::REFUND_REFUNDED])
                ->get()
                ->sum(fn (Refund $refund) => Money::toMinor($refund->getAttribute('amount'), $currency));

            $availableMinor = Money::toMinor($checkout->getAttribute('amount'), $currency) - $reservedMinor;
            $amountMinor = $amount === null ? $availableMinor : Money::toMinor($amount, $currency);

            if ($amountMinor <= 0 || $amountMinor > $availableMinor) {
                throw new TapException(sprintf(
                    'Refund amount must be greater than 0 and at most %s.',
                    Money::format(max($availableMinor, 0), $currency)
                ));
            }

            return Refund::create([
                'title' => 'Tap refund',
                'amount' => Money::toFloat($amountMinor, $currency),
                'refundable_id' => $checkout->getKey(),
                'refundable_type' => PaymentGatewayCheckout::class,
                'status' => self::REFUND_PENDING,
            ]);
        });

        $currency = $this->checkoutCurrency($checkout);

        try {
            $response = $this->client->createRefund([
                'charge_id' => $checkout->getAttribute('gateway_reference'),
                'amount' => (float) $refund->getAttribute('amount'),
                'currency' => $currency,
                'reason' => $reason,
                'reference' => ['merchant' => $refund->getAttribute('uuid')],
                'metadata' => ['checkout_uuid' => $checkout->getAttribute('uuid')],
                'post' => ['url' => $this->webhookUrl()],
            ]);
        } catch (TapException $exception) {
            $refund->update(['status' => self::REFUND_FAILED]);

            throw $exception;
        }

        $refund->update(['gateway_reference' => $response['id'] ?? null]);

        return $this->applyRefund($response);
    }

    /**
     * Fetch the refund from Tap and apply its status
     *
     * @param string $refundId
     *
     * @return Refund
     *
     * @throws TapException
     */
    public function syncRefund(string $refundId): Refund
    {
        return $this->applyRefund($this->client->retrieveRefund($refundId));
    }

    /**
     * Safety net for missed webhooks: re-check recent open charges and refunds
     *
     * @return array{charges: int, refunds: int, errors: int}
     */
    public function syncPending(): array
    {
        $counts = ['charges' => 0, 'refunds' => 0, 'errors' => 0];

        PaymentGatewayCheckout::query()
            ->where('payment_gateway', PaymentGatewaysEnum::TAP->value)
            ->where('is_processed', false)
            ->whereNotNull('gateway_reference')
            ->where('created_at', '>=', now()->subDays(3))
            ->each(function (PaymentGatewayCheckout $checkout) use (&$counts) {
                try {
                    $this->syncCharge($checkout->getAttribute('gateway_reference'));
                    $counts['charges']++;
                } catch (Throwable $exception) {
                    report($exception);
                    $counts['errors']++;
                }
            });

        Refund::query()
            ->where('refundable_type', PaymentGatewayCheckout::class)
            ->where('status', self::REFUND_PENDING)
            ->whereNotNull('gateway_reference')
            ->each(function (Refund $refund) use (&$counts) {
                try {
                    $this->syncRefund($refund->getAttribute('gateway_reference'));
                    $counts['refunds']++;
                } catch (Throwable $exception) {
                    report($exception);
                    $counts['errors']++;
                }
            });

        return $counts;
    }

    /**
     * Create the order exactly like the MyFatoorah webhook does. Exceptions bubble up
     * so the transaction rolls back and the webhook / sync retries later.
     *
     * @param PaymentGatewayCheckout $checkout
     *
     * @return void
     */
    private function fulfil(PaymentGatewayCheckout $checkout): void
    {
        $payable = $checkout->payable;

        if ($payable instanceof Order) {
            app(RenewOrderService::class)->store($checkout, $payable, $checkout->user);
        } elseif ($payable instanceof ServicePackage) {
            app(OrderService::class)->store($checkout, $payable, $checkout->user);
        } elseif ($payable instanceof Project) {
            app(OrderService::class)->storeProject($checkout, $payable, $checkout->user);
        } else {
            throw new TapException("Unsupported payable [{$checkout->getAttribute('payable_type')}].");
        }
    }

    private function applyRefund(array $data): Refund
    {
        return DB::transaction(function () use ($data) {
            $refund = Refund::where('gateway_reference', $data['id'] ?? null)->lockForUpdate()->first()
                ?? Refund::where('uuid', data_get($data, 'reference.merchant'))->lockForUpdate()->first();

            if (! $refund) {
                throw new TapUnknownReferenceException('Unknown Tap refund ['.($data['id'] ?? '?').'].');
            }

            if (in_array($refund->getAttribute('status'), [self::REFUND_REFUNDED, self::REFUND_FAILED, self::REFUND_FLAGGED], true)) {
                return $refund;
            }

            $status = strtoupper((string) ($data['status'] ?? 'UNKNOWN'));
            $refund->setAttribute('gateway_reference', $refund->getAttribute('gateway_reference') ?? ($data['id'] ?? null));

            if ($status === 'REFUNDED') {
                $currency = strtoupper((string) ($data['currency'] ?? ''));
                $matches = $currency !== ''
                    && Money::toMinor($data['amount'] ?? 0, $currency) === Money::toMinor($refund->getAttribute('amount'), $currency);

                $refund->setAttribute('status', $matches ? self::REFUND_REFUNDED : self::REFUND_FLAGGED);

                if (! $matches) {
                    Log::critical('Tap refund does not match the requested refund', [
                        'refund' => $refund->getAttribute('uuid'),
                        'expected' => $refund->getAttribute('amount'),
                        'received' => [$data['amount'] ?? null, $data['currency'] ?? null],
                    ]);
                }
            } elseif (in_array($status, self::REFUND_FAILURES, true)) {
                $refund->setAttribute('status', self::REFUND_FAILED);
            }

            $refund->save();

            return $refund;
        });
    }

    private function chargeMatchesCheckout(array $charge, PaymentGatewayCheckout $checkout): bool
    {
        $currency = strtoupper((string) ($charge['currency'] ?? ''));

        return $currency === $this->checkoutCurrency($checkout)
            && Money::toMinor($charge['amount'] ?? 0, $currency) === Money::toMinor($checkout->getAttribute('amount'), $currency);
    }

    private function refundsOf(PaymentGatewayCheckout $checkout)
    {
        return Refund::query()
            ->where('refundable_type', PaymentGatewayCheckout::class)
            ->where('refundable_id', $checkout->getKey());
    }

    private function customer(User $user): array
    {
        $name = trim((string) $user->getAttribute('name')) ?: 'Customer';
        $parts = preg_split('/\s+/u', $name, 2);

        return array_filter([
            'first_name' => $parts[0],
            'last_name' => $parts[1] ?? null,
            'email' => $user->getAttribute('email'),
        ]);
    }

    private function checkoutCurrency(PaymentGatewayCheckout $checkout): string
    {
        return strtoupper((string) ($checkout->getAttribute('currency') ?: $this->currency()));
    }

    private function currency(): string
    {
        return strtoupper((string) config('tap.currency', 'USD'));
    }

    private function webhookUrl(): string
    {
        return config('tap.webhook_url') ?: route('tap.webhook');
    }

    private function returnUrl(): string
    {
        return config('tap.return_url') ?: route('tap.return');
    }
}
