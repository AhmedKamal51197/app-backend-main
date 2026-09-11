<?php

namespace App\Support;

use InvalidArgumentException;

/**
 * Converts amounts to integer minor units (cents, halalas, fils) so that
 * comparisons with the payment gateway never suffer from float rounding.
 */
final class Money
{
    private const THREE_DECIMAL_CURRENCIES = ['BHD', 'IQD', 'JOD', 'KWD', 'LYD', 'OMR', 'TND'];

    public static function decimals(string $currency): int
    {
        return in_array(strtoupper($currency), self::THREE_DECIMAL_CURRENCIES, true) ? 3 : 2;
    }

    public static function toMinor(string|int|float $amount, string $currency): int
    {
        if (! is_numeric($amount)) {
            throw new InvalidArgumentException("Invalid amount [{$amount}].");
        }

        return (int) round(((float) $amount) * (10 ** self::decimals($currency)));
    }

    public static function toFloat(int $minor, string $currency): float
    {
        $decimals = self::decimals($currency);

        return round($minor / (10 ** $decimals), $decimals);
    }

    public static function format(int $minor, string $currency): string
    {
        $decimals = self::decimals($currency);

        return number_format($minor / (10 ** $decimals), $decimals, '.', '').' '.strtoupper($currency);
    }
}
