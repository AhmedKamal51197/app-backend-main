<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

/**
 * Seed the configurable payment-gateway (Tap) fee charged to the buyer at
 * checkout. Stored as percentage + fixed so the admin can match Tap's rate.
 */
return new class extends Migration
{
    public function up(): void
    {
        Setting::firstOrCreate(
            ['setting_name' => 'gateway_fee_percentage'],
            ['setting_value' => '2.75']
        );

        Setting::firstOrCreate(
            ['setting_name' => 'gateway_fee_fixed'],
            ['setting_value' => '0']
        );
    }

    public function down(): void
    {
        Setting::whereIn('setting_name', ['gateway_fee_percentage', 'gateway_fee_fixed'])->delete();
    }
};
