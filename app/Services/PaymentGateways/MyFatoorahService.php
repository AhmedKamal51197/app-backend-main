<?php

namespace App\Services\PaymentGateways;

use App\Events\LogExceptionEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use App\Models\User;

/**
 * The service for payment
 */
class MyFatoorahService
{
    /**
     * Create MyFatoorah checkout
     *
     * @param User $user
     * @param Model $model
     *
     * @return array
     *
     * @throws \Exception
     */
    public function checkout(User $user, Model $model): array
    {
        $payload = [
            "PaymentType" => "card",
            "InvoiceValue" => $model->getAttribute('amount'),
            "CustomerName" => $user->getAttribute('name'),
            "NotificationOption" => "LNK",
            // "CustomerMobile" => $this->parsePhoneNumber($user->getAttribute('mobile')),
            "CustomerMobile" => '123456789', // Temporary fix for mobile number
            "MobileCountryCode" => "+966",
            "DisplayCurrencyIso" => "SAR",
            "ExpiryDate" => $this->parseExpireDate(),
            "CustomerReference" => $model->getAttribute('uuid'),
            "WebhookUrl" => config('myfatoorah.endpoints.webhook'),
            "SuccessUrl" => config('myfatoorah.endpoints.success'),
            "FailureUrl" => config('myfatoorah.endpoints.failure'),
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
            ->post(config('myfatoorah.endpoints.checkout'), $payload);

            if ($response->successful() && $response['IsSuccess']) {
                return [
                    'status' => true,
                    'id' => $response['Data']['InvoiceId'],
                    'reference' => $model->getAttribute('uuid'),
                    'url' => $response['Data']['InvoiceURL'],
                ];
            } else {

                throw new \Exception(__("Request checkout failed"));
            }

        } catch (\Exception $exception) {
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

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
     * Parse the phone number to be good with payment gateway
     *
     * @param $phone
     *
     * @return string
     */
    private function parsePhoneNumber($phone): string
    {
        $countryCode = '966';

        return str_starts_with($phone, $countryCode) ? substr($phone, strlen($countryCode)) : $phone;
    }

    private function parseExpireDate(): string
    {
        return now()->addMonth()->toIso8601String();
    }
}
