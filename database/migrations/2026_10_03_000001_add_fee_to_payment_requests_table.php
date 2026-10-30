<?php

use App\Enums\SettingsEnum;
use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * The withdrawal transfer fee is borne by the freelancer: the wallet is
     * debited by the gross `amount`, the `fee` is kept to cover the manual
     * transfer cost, and `net_amount` is what is actually sent to the bank.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_requests', function (Blueprint $table) {
            $table->double('fee')->default(0)->after('amount');
            $table->double('net_amount')->nullable()->after('fee');
        });

        // Default transfer fee for manual local (SARIE) bank payouts in KSA.
        // A flat 2 SAR covers the ~1 SAR local-transfer cost with a small buffer;
        // no percentage. Adjustable anytime from the admin withdrawal settings.
        // firstOrCreate keeps any value the admin may have already configured.
        Setting::firstOrCreate(
            ['setting_name' => SettingsEnum::WITHDRAWAL_FEE_FIXED->value],
            ['setting_value' => '2']
        );

        Setting::firstOrCreate(
            ['setting_name' => SettingsEnum::WITHDRAWAL_FEE_PERCENTAGE->value],
            ['setting_value' => '0']
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_requests', function (Blueprint $table) {
            $table->dropColumn(['fee', 'net_amount']);
        });

        Setting::whereIn('setting_name', [
            SettingsEnum::WITHDRAWAL_FEE_FIXED->value,
            SettingsEnum::WITHDRAWAL_FEE_PERCENTAGE->value,
        ])->delete();
    }
};
