<?php

namespace App\Services\Payment\Tap;

use App\Support\Money;

/**
 * Validates the "hashstring" header Tap sends with charge and refund webhooks
 *
 * @see https://developers.tap.company/docs/webhook
 */
final class TapSignature
{
    public static function sign(array $payload, string $secretKey): string
    {
        $currency = (string) ($payload['currency'] ?? '');
        $amount = number_format((float) ($payload['amount'] ?? 0), Money::decimals($currency), '.', '');

        $message = 'x_id'.($payload['id'] ?? '')
            .'x_amount'.$amount
            .'x_currency'.$currency
            .'x_gateway_reference'.data_get($payload, 'reference.gateway', '')
            .'x_payment_reference'.data_get($payload, 'reference.payment', '')
            .'x_status'.($payload['status'] ?? '')
            .'x_created'.data_get($payload, 'transaction.created', $payload['created'] ?? '');

        return hash_hmac('sha256', $message, $secretKey);
    }

    public static function verify(array $payload, ?string $signature, string $secretKey): bool
    {
        if ($secretKey === '' || $signature === null || trim($signature) === '') {
            return false;
        }

        return hash_equals(self::sign($payload, $secretKey), strtolower(trim($signature)));
    }
}
