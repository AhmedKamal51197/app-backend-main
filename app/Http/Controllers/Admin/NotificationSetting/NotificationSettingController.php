<?php

namespace App\Http\Controllers\Admin\NotificationSetting;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Resources\Api\Admin\NotificationSetting\NotificationSettingResource;
use App\Models\NotificationSetting;
use App\Services\Admin\NotificationSettingService;
use Illuminate\Http\JsonResponse;

/**
 * A class defines the notification setting controller
 */
class NotificationSettingController extends BaseAdminController
{
    public function __construct(private NotificationSettingService $service)
    {
    }

    /**
     * Get all notification settings
     */
    public function index(): JsonResponse
    {
        $settings = $this->service->index();

        return $this->jsonSuccess(NotificationSettingResource::collection($settings));
    }

    /**
     * Toggle notification setting status
     */
    public function toggleStatus(NotificationSetting $notificationSetting): JsonResponse
    {
        $setting = $this->service->toggleStatus($notificationSetting);

        return $this->jsonSuccess(new NotificationSettingResource($setting), 'Notification setting updated successfully');
    }
}
