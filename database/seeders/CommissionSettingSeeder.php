<?php

namespace Database\Seeders;

use App\Models\CommissionSetting;
use Illuminate\Database\Seeder;

/**
 * A class defines the commission setting seeder
 */
class CommissionSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        CommissionSetting::updateOrCreate([
            'key' => 'provider',
            'value' => 0.12,
            'description' => 'Provider commissions',
        ]);

        CommissionSetting::updateOrCreate([
            'key' => 'seeker',
            'value' => 0.065,
            'description' => 'Seeker commissions',
        ]);

        CommissionSetting::updateOrCreate([
            'key' => 'seeker_renew',
            'value' => 0.065,
            'description' => 'Seeker renew commissions',
        ]);

        CommissionSetting::updateOrCreate([
            'key' => 'minimum_withdrawal_limit',
            'value' => 100,
            'description' => 'Minimum withdrawal limit',
        ]);

        CommissionSetting::updateOrCreate([
            'key' => 'daily_withdrawal_limit',
            'value' => 250,
            'description' => 'Daily withdrawal limit',
        ]);

        CommissionSetting::updateOrCreate([
            'key' => 'monthly_withdrawal_limit',
            'value' => 2000,
            'description' => 'Monthly withdrawal limit',
        ]);
    }
}
