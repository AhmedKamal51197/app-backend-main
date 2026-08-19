<?php

namespace App\Services\Admin;

use App\Models\NotificationSetting;
use Illuminate\Database\Eloquent\Collection;

/**
 * A class defines the notification setting service
 */
class NotificationSettingService
{
    /**
     * Get all notification settings
     * @return Collection
     */
    public function index(): Collection
    {
        return NotificationSetting::orderBy('setting_key')->get();
    }

    /**
     * Toggle notification setting status
     *
     * @param NotificationSetting $notificationSetting
     *
     * @return NotificationSetting
     */
    public function toggleStatus(NotificationSetting $notificationSetting): NotificationSetting
    {
        $notificationSetting->update(['is_enabled' => !$notificationSetting->is_enabled]);


        return $notificationSetting;
    }
}
