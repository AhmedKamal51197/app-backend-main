<?php

namespace App\Services\Setting;

use App\Actions\Attachments\StoreAttachmentAction;
use App\Enums\SettingsEnum;
use App\Models\Setting;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * A class defines the setting service
 */
class SettingService
{
    /**
     * Get all settings for admin
     *
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function index(int $perPage): LengthAwarePaginator
    {
        return Setting::query()
            ->with(['attachment'])
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Update setting
     *
     * @param Setting $setting
     * @param array $data
     *
     * @return Setting
     */
    public function update(Setting $setting, array $data): Setting
    {
        if ($data['type'] === 'attachment') {
            StoreAttachmentAction::store($setting, $data['attachment'], 'attachment', true);
            
            return $setting->fresh(['attachment']);
        } else {
            $setting->update(['setting_value' => $data['setting_value']]);

            return $setting->fresh();
        }
    }

    /**
     * Delete setting
     *
     * @param Setting $setting
     *
     * @return bool
     */
    public function delete(Setting $setting): bool
    {
        return $setting->delete();
    }

    /**
     * Get attachment disk setting
     *
     * @return string
     */
    public static function getAttachmentStorage(): string
    {
        $setting = Setting::where('setting_name', 'attachment_storage')->first();

        return $setting?->setting_value ?? Setting::ATTACHMENT_STORAGE->value;
    }

    /**
     * Update withdrawal settings
     *
     * @param array $data
     *
     * @return void
     */
    public function updateWithdrawalSettings(array $data): void
    {
        Setting::updateOrCreate(
            ['setting_name' => 'daily_payment_requests_amount'],
            ['setting_value' => $data['daily_payment_requests_amount']]
        );

        Setting::updateOrCreate(
            ['setting_name' => 'monthly_payment_requests_amount'],
            ['setting_value' => $data['monthly_payment_requests_amount']]
        );

        Setting::updateOrCreate(
            ['setting_name' => 'minimum_payment_request_amount'],
            ['setting_value' => $data['minimum_payment_request_amount']]
        );

        if (array_key_exists('withdrawal_fee_fixed', $data)) {
            Setting::updateOrCreate(
                ['setting_name' => SettingsEnum::WITHDRAWAL_FEE_FIXED->value],
                ['setting_value' => $data['withdrawal_fee_fixed']]
            );
        }

        if (array_key_exists('withdrawal_fee_percentage', $data)) {
            Setting::updateOrCreate(
                ['setting_name' => SettingsEnum::WITHDRAWAL_FEE_PERCENTAGE->value],
                ['setting_value' => $data['withdrawal_fee_percentage']]
            );
        }
    }

    /**
     * Read the configured withdrawal transfer fee (fixed amount + percentage).
     *
     * @return array{fixed: float, percentage: float}
     */
    public static function withdrawalFeeConfig(): array
    {
        return [
            'fixed' => (float) (Setting::where('setting_name', SettingsEnum::WITHDRAWAL_FEE_FIXED->value)->value('setting_value') ?? 0),
            'percentage' => (float) (Setting::where('setting_name', SettingsEnum::WITHDRAWAL_FEE_PERCENTAGE->value)->value('setting_value') ?? 0),
        ];
    }

    /**
     * Calculate the transfer fee charged to the freelancer for a withdrawal.
     * The fee never exceeds the requested amount.
     *
     * @param float $amount
     *
     * @return float
     */
    public static function calculateWithdrawalFee(float $amount): float
    {
        $config = self::withdrawalFeeConfig();

        $fee = $config['fixed'] + ($amount * $config['percentage'] / 100);

        return round(min($fee, $amount), 2);
    }
}
