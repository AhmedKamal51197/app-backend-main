<?php

namespace App\Http\Controllers\Api\Checkout;

use App\Enums\PaymentGatewaysEnum;
use App\Events\LogExceptionEvent;
use App\Http\Controllers\Api\BaseApiController;
use App\Models\PaymentGateway\PaymentGatewayCheckout;
use App\Services\Payment\TapService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Throwable;

/**
 * A class defines the checkout payment status controller
 *
 * The mobile app calls this after the payment page closes. The status in the
 * redirect URL is display-only; this endpoint is the source of truth.
 */
class CheckoutStatusController extends BaseApiController
{
    /**
     * Tap statuses after which the checkout will never be paid
     */
    private const FAILED_STATUSES = [
        'ABANDONED', 'CANCELLED', 'DECLINED', 'FAILED', 'RESTRICTED', 'TIMEDOUT', 'VOID', 'AMOUNT_MISMATCH',
    ];

    /**
     * Call the service
     *
     * @param TapService $tapService
     */
    public function __construct(protected TapService $tapService)
    {
    }

    /**
     * Show the payment status of the authenticated user's checkout
     *
     * @param string $reference Checkout UUID returned by the checkout endpoints
     *
     * @return JsonResponse
     */
    public function show(string $reference): JsonResponse
    {
        $checkout = PaymentGatewayCheckout::query()
            ->where('uuid', $reference)
            ->where('user_id', request()->user()->getAttribute('id'))
            ->first();

        if (! $checkout) {
            return $this->jsonError(__('Checkout not found'), 404);
        }

        // The webhook may not have arrived yet: ask Tap directly so the app never waits on it.
        if ($checkout->getAttribute('payment_gateway') === PaymentGatewaysEnum::TAP->value
            && ! $checkout->getAttribute('is_processed')
            && $checkout->getAttribute('gateway_reference')) {
            try {
                $checkout = $this->tapService->syncCharge($checkout->getAttribute('gateway_reference'));
            } catch (Throwable $exception) {
                event(new LogExceptionEvent($exception));
            }
        }

        $paidAt = $checkout->getAttribute('paid_at');

        $status = $this->status($checkout);

        return $this->jsonSuccess([
            'reference' => $checkout->getAttribute('uuid'),
            'status' => $status,
            // Present in every response; carries the failure cause when the payment did not go through.
            'reason' => $this->reason($checkout, $status),
            'gateway' => $checkout->getAttribute('payment_gateway'),
            'gateway_status' => $checkout->getAttribute('gateway_status'),
            'amount' => (float) $checkout->getAttribute('amount'),
            'currency' => $checkout->getAttribute('currency'),
            'paid_at' => $paidAt ? Carbon::parse($paidAt)->toIso8601String() : null,
        ]);
    }

    /**
     * paid | failed | pending for Tap checkouts
     *
     * @param PaymentGatewayCheckout $checkout
     *
     * @return string
     */
    private function status(PaymentGatewayCheckout $checkout): string
    {
        if ($checkout->getAttribute('payment_gateway') !== PaymentGatewaysEnum::TAP->value) {
            return 'unsupported';
        }

        if ($checkout->getAttribute('is_processed')) {
            return 'paid';
        }

        return in_array(strtoupper((string) $checkout->getAttribute('gateway_status')), self::FAILED_STATUSES, true)
            ? 'failed'
            : 'pending';
    }

    /**
     * Human-facing reason for the current status. Null unless the payment failed.
     *
     * @param PaymentGatewayCheckout $checkout
     * @param string $status
     *
     * @return string|null
     */
    private function reason(PaymentGatewayCheckout $checkout, string $status): ?string
    {
        if ($status !== 'failed') {
            return null;
        }

        return (string) $checkout->getAttribute('gateway_status') ?: 'failed';
    }
}
