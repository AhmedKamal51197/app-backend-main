<?php

namespace App\Http\Controllers\Admin\DashboardNotification;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Requests\Admin\DashboardNotification\StoreDashboardNotificationRequest;
use App\Http\Requests\Admin\DashboardNotification\UpdateDashboardNotificationRequest;
use App\Http\Resources\Admin\DashboardNotification\DashboardNotificationResource;
use App\Models\DashboardNotification;
use App\Services\Notification\DashboardNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defines the dashboard notification controller
 */
class DashboardNotificationController extends BaseAdminController
{
    /**
     * DashboardNotificationController constructor
     *
     * @param DashboardNotificationService $service
     */
    public function __construct(protected DashboardNotificationService $service)
    {
    }

    /**
     * List all dashboard notifications
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $notifications = $this->service->index($request->all(), $request->get('limit', 15));

        return $this->jsonSuccess(DashboardNotificationResource::collection($notifications));
    }

    /**
     * Store a new dashboard notification
     *
     * @param StoreDashboardNotificationRequest $request
     *
     * @return JsonResponse
     */
    public function store(StoreDashboardNotificationRequest $request): JsonResponse
    {
        $notification = $this->service->store($request->validated());

        return $this->jsonSuccess(DashboardNotificationResource::make($notification), __('Notification created successfully'));
    }

    /**
     * Show dashboard notification details
     *
     * @param DashboardNotification $notification
     *
     * @return JsonResponse
     */
    public function show(DashboardNotification $notification): JsonResponse
    {
        return $this->jsonSuccess(DashboardNotificationResource::make($notification));
    }

    /**
     * Update dashboard notification
     *
     * @param UpdateDashboardNotificationRequest $request
     * @param DashboardNotification $notification
     *
     * @return JsonResponse
     */
    public function update(UpdateDashboardNotificationRequest $request, DashboardNotification $notification): JsonResponse
    {
        $notification = $this->service->update($notification, $request->validated());

        return $this->jsonSuccess(DashboardNotificationResource::make($notification), __('Notification updated successfully'));
    }

    /**
     * Delete dashboard notification
     *
     * @param DashboardNotification $notification
     *
     * @return JsonResponse
     */
    public function destroy(DashboardNotification $notification): JsonResponse
    {
        $this->service->delete($notification);

        return $this->jsonSuccess(null, __('Notification deleted successfully'));
    }

    /**
     * Mark notification as seen
     *
     * @param DashboardNotification $notification
     *
     * @return JsonResponse
     */
    public function markAsSeen(DashboardNotification $notification): JsonResponse
    {
        $notification = $this->service->markAsSeen($notification);

        return $this->jsonSuccess(DashboardNotificationResource::make($notification), __('Notification marked as seen'));
    }

    /**
     * Mark notification as resolved
     *
     * @param DashboardNotification $notification
     *
     * @return JsonResponse
     */
    public function markAsResolved(DashboardNotification $notification): JsonResponse
    {
        $notification = $this->service->markAsResolved($notification);

        return $this->jsonSuccess(DashboardNotificationResource::make($notification), __('Notification marked as resolved'));
    }
}
