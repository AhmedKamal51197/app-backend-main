<?php

namespace App\Services\Payment;

use App\Events\LogExceptionEvent;
use App\Models\Order;
use App\Models\PaymentGateway\PaymentGatewayCheckout;
use App\Models\Project;
use App\Models\ServicePackage;
use App\Services\Order\OrderService;
use App\Services\Order\RenewOrderService;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use App\Models\User;

/**
 * The service for MyFatoorah Service
 */
class MyFatoorahService
{
    protected RenewOrderService $renewOrderService;

    /**
     * Load the order service
     */
    public function __construct(protected OrderService $orderService)
    {
        $this->renewOrderService = new RenewOrderService($this);
    }

    /**
     * Create MyFatoorah checkout
     *
     * @param User $user
     * @param Model $model
     * @param $paymentMethodId
     *
     * @return array
     *
     * @throws Exception
     */
    public function checkout(User $user, Model $model, $paymentMethodId): array
    {
        $payload = [
            "PaymentType" => "card",
            "InvoiceValue" => $model->getAttribute('amount'),
            "CustomerName" => $user->getAttribute('name'),
            "NotificationOption" => "LNK",
            "CustomerMobile" => '123456789',
            "MobileCountryCode" => "+966",
            "DisplayCurrencyIso" => "USD",
            "PaymentMethodId" => $paymentMethodId,
            "ExpiryDate" => $this->parseExpireDate(),
            "CustomerReference" => $model->getAttribute('uuid'),
            "WebhookUrl" => config('myfatoorah.endpoints.webhook'),
            "SuccessUrl" => config('myfatoorah.endpoints.success'),
            "FailureUrl" => config('myfatoorah.endpoints.failure'),
            "CallbackUrl" => config('myfatoorah.endpoints.success'),
            "ErrorUrl" => config('myfatoorah.endpoints.failure'),
            "InvoiceItems" => [
                [
                    "ItemName" => $model->getAttribute('id'),
                    "Quantity" => 1,
                    "UnitPrice" => $model->getAttribute('amount'),
                ]
            ]
        ];

        try {
            $response = Http::withToken(config('myfatoorah.token'))
                ->post(config('myfatoorah.endpoints.initiate_session'), $payload);

            if ($response->successful() && $response['IsSuccess']) {

                $model->update([
                    'invoice_id' => "dada"//$response['Data']['SessionId']
                ]);

                return [
                    'status' => true,
//                    'id' => $response['Data']['InvoiceId'],
                    'reference' => $model->getAttribute('uuid'),
                    'url' => $response['Data']['PaymentURL'],
                ];

            } else {
                error_log($response);
                throw new \Exception(__("Request checkout failed"));
            }

        } catch (\Exception $exception) {
            error_log($exception->getMessage());
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Get available payment methods from MyFatoorah
     **
     * @return array
     *
     * @throws Exception
     */
    public function getPaymentMethods(): array
    {
        $payload = [
            "CurrencyIso" => "USD"
        ];

        try {
            $response = Http::withToken(config('myfatoorah.token'))
                ->post(config('myfatoorah.endpoints.initiate_payment'), $payload);

            if ($response->successful() && $response['IsSuccess']) {
                return [
                    'status' => true,
                    'paymentMethods' => $response['Data']['PaymentMethods']
                ];
            }

            throw new \Exception(__('Failed to fetch payment methods'));
        } catch (\Exception $exception) {
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }


    /**
     * Check if the checkout is paid or not
     *
     * @param string $invoiceId
     *
     * @return bool
     */
    public function checkIsPaid(string $invoiceId): bool
    {
        $payload = [
            "Key" => $invoiceId,
            "KeyType" => "InvoiceId"
        ];

        try {
            $response = Http::withToken(config('myfatoorah.token'))
                ->post(config('myfatoorah.endpoints.payment_status'), $payload);

            if ($response->successful() && $response['IsSuccess']) {
                $data = $response->json();
                if ($data['Data']['InvoiceStatus'] === "Paid") {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (\Exception $exception) {
            event(new LogExceptionEvent($exception));

            return false;
        }
    }

    /**
     * Parse the expiry date
     *
     * @return string
     */
    private function parseExpireDate(): string
    {
        return now()->addMonth()->toIso8601String();
    }

    /**
     * Adding store subscription
     *
     * @param PaymentGatewayCheckout $checkout
     *
     * @return void
     *
     * @throws Exception
     */
    public function webhook(PaymentGatewayCheckout $checkout): void
    {
        try {
            if ($checkout->payable instanceof Order) {
                $this->renewOrderService->store($checkout, $checkout->payable, $checkout->user);
            } else if ($checkout->payable instanceof ServicePackage) {
                $this->orderService->store($checkout, $checkout->payable, $checkout->user);
            } else if ($checkout->payable instanceof Project) {
                $this->orderService->storeProject($checkout, $checkout->payable, $checkout->user);
            }
        } catch (\Exception $exception) {
            event(new LogExceptionEvent($exception));
        }
    }
}
