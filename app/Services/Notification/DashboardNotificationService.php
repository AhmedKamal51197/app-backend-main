<?php

namespace App\Services\Notification;

use App\Events\LogExceptionEvent;
use App\Models\DashboardNotification;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * A class defines the dashboard notification service
 */
class DashboardNotificationService
{
    /**
     * Index dashboard notifications with filters
     *
     * @param array $filters
     * @param int $limit
     *
     * @return LengthAwarePaginator
     */
    public function index(array $filters, int $limit): LengthAwarePaginator
    {
        return DashboardNotification::query()
            ->when($filters['type'] ?? null, fn($q, $type) => $q->where('type', $type))
            ->when(isset($filters['resolved']), fn($q) => $q->where('resolved', $filters['resolved']))
            ->when(isset($filters['is_seen']), fn($q) => $q->where('is_seen', $filters['is_seen']))
            ->latest()
            ->paginate($limit);
    }

    /**
     * Store new dashboard notification
     *
     * @param array $data
     *
     * @return DashboardNotification
     *
     * @throws Exception
     */
    public function store(array $data): DashboardNotification
    {
        DB::beginTransaction();
        try {
            $notification = DashboardNotification::create([
                'title' => $data['title'],
                'details' => $data['details'] ?? null,
                'type' => $data['type'],
                'resolved' => $data['resolved'] ?? false,
                'is_seen' => $data['is_seen'] ?? false,
            ]);

            DB::commit();

            return $notification;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Update dashboard notification
     *
     * @param DashboardNotification $notification
     * @param array $data
     *
     * @return DashboardNotification
     *
     * @throws Exception
     */
    public function update(DashboardNotification $notification, array $data): DashboardNotification
    {
        DB::beginTransaction();
        try {
            $notification->update([
                'title' => $data['title'] ?? $notification->title,
                'details' => $data['details'] ?? $notification->details,
                'type' => $data['type'] ?? $notification->type,
                'resolved' => $data['resolved'] ?? $notification->resolved,
                'is_seen' => $data['is_seen'] ?? $notification->is_seen,
            ]);

            DB::commit();

            return $notification;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Delete dashboard notification
     *
     * @param DashboardNotification $notification
     *
     * @return bool
     *
     * @throws Exception
     */
    public function delete(DashboardNotification $notification): bool
    {
        DB::beginTransaction();
        try {
            $deleted = $notification->delete();

            DB::commit();

            return $deleted;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Mark notification as seen
     *
     * @param DashboardNotification $notification
     *
     * @return DashboardNotification
     *
     * @throws Exception
     */
    public function markAsSeen(DashboardNotification $notification): DashboardNotification
    {
        return $this->update($notification, ['is_seen' => true]);
    }

    /**
     * Mark notification as resolved
     *
     * @param DashboardNotification $notification
     *
     * @return DashboardNotification
     *
     * @throws Exception
     */
    public function markAsResolved(DashboardNotification $notification): DashboardNotification
    {
        return $this->update($notification, ['resolved' => true]);
    }
}
