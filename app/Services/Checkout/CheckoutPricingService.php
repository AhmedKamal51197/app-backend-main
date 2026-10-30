<?php

namespace App\Services\Checkout;

use App\Models\CommissionSetting;
use App\Models\Setting;

/**
 * Computes the checkout price breakdown shown to the buyer before payment:
 * the service price, the platform commission, the (non-refundable) payment
 * gateway fee, and the grand total that is actually charged.
 */
class CheckoutPricingService
{
    /**
     * Full breakdown for a given base (service/proposal/package) price.
     *
     * @param float $basePrice
     *
     * @return array<string, mixed>
     */
    public function breakdown(float $basePrice): array
    {
        $basePrice = round((float) $basePrice, 2);

        $commissionRate = (float) (CommissionSetting::where('key', 'seeker')->value('value') ?? 0);
        $commission = round($basePrice * $commissionRate, 2);

        $subtotal = round($basePrice + $commission, 2);

        $gatewayFee = self::gatewayFee($subtotal);
        $total = round($subtotal + $gatewayFee, 2);

        return [
            'service_price' => $basePrice,
            'platform_commission' => $commission,
            'gateway_fee' => $gatewayFee,
            'gateway_fee_refundable' => false,
            'total' => $total,
        ];
    }

    /**
     * The grand total that must be charged (base + commission + gateway fee).
     *
     * @param float $basePrice
     *
     * @return float
     */
    public function total(float $basePrice): float
    {
        return $this->breakdown($basePrice)['total'];
    }

    /**
     * Payment gateway (Tap) fee on an amount, from configurable settings
     * (percentage + fixed) so the admin can match Tap's actual rate.
     *
     * @param float $amount
     *
     * @return float
     */
    public static function gatewayFee(float $amount): float
    {
        $percentage = (float) (Setting::where('setting_name', 'gateway_fee_percentage')->value('setting_value') ?? 0);
        $fixed = (float) (Setting::where('setting_name', 'gateway_fee_fixed')->value('setting_value') ?? 0);

        return round(($amount * $percentage / 100) + $fixed, 2);
    }
}
