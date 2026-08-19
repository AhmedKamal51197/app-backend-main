<?php

namespace App\Services\Report;

use App\Events\LogExceptionEvent;
use App\Models\Report;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\Reports\NewReportSubmittedNotification;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Class Rate service to handle the reports
 */
class ReportService
{
    /**
     * Get all reports
     *
     * @param int $perPage
     * @param array $filters
     *
     * @return LengthAwarePaginator
     */
    public function index(int $perPage, array $filters = []): LengthAwarePaginator
    {
        $query = Report::query()
            ->withCount('responses')
            ->with(['user', 'latestResponse.sender']);

        if (!empty($filters['status'])) {
            $query->whereRaw("LOWER(status) = ?", [strtolower($filters['status'])]);
        }

        if (!empty($filters['role'])) {
            $query->whereHas('user', function ($q) use ($filters) {
                $q->whereHas('roles', function ($roleQuery) use ($filters) {
                    $roleQuery->where('name', $filters['role']);
                });
            });
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('body', 'like', '%' . $filters['search'] . '%')
                    ->orWhereHas('user', function ($userQuery) use ($filters) {
                        $userQuery->where('name', 'like', '%' . $filters['search'] . '%');
                    });
            });
        }

        $query->orderBy('updated_at', $filters['orderBy'] === 'oldest' ? 'asc' : 'desc');


        return $query->paginate($perPage);
    }

    /**
     * Get all reports for user
     *
     * @param User $user
     * @param int $perPage
     * @param string $status
     *
     * @return LengthAwarePaginator
     */
    public function userReports(User $user, int $perPage, string $status): LengthAwarePaginator
    {
        $query = Report::query()
            ->where('user_id', $user->getAttribute('id'))
            ->withCount('responses')
            ->with(['latestResponse.sender']);

        if (!empty($status)) {
            $query->where('status', $status);
        }

        return $query->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Store report
     *
     * @param User $user
     * @param array $data
     *
     * @return Report
     *
     * @throws Exception
     */
    public function store(User $user, array $data): Report
    {
        DB::beginTransaction();
        try {

            $report = Report::create([
                'title' => $data['title'],
                'body' => $data['body'],
                'user_id' => $user->getAttribute('id'),
            ]);

            DB::commit();

           $user->notify(new NewReportSubmittedNotification());

            return $report->fresh();

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Store report by admin for a user (admin starts chat with user)
     *
     * @param array $data
     *
     * @return Report
     *
     */
    public function storeForUser(array $data): Report
    {
            $user = User::where('uuid', $data['user_id'])->firstOrFail();

            $report = Report::create([
                'title' => $data['title'],
                'body' => $data['body'],
                'user_id' => $user->getAttribute('id'),
            ]);

            $user->notify(new NewReportSubmittedNotification());

            return $report->fresh(['user']);
    }

    /**
     * Update report status
     *
     * @param Report $report
     *
     * @return Report
     *
     */
    public function updateStatus(Report $report): Report
    {
        $report->update(['status' => $report->status->value === 'opened' ? 'closed' : 'opened']);

        return $report->fresh();
    }

    /**
     * Close report
     *
     * @param Report $report
     *
     * @return Report
     *
     * @throws Exception
     */
    public function closeReport(Report $report): Report
    {
        $report->update(['status' => 'closed']);

        return $report->fresh();
    }

    /**
     * Show report with responses
     *
     * @param Report $report
     * @param string $search
     *
     * @return Report
     */
    public function show(Report $report, string $search = ''): Report
    {
        $report->load([
            'user',
            'responses' => function ($query) use ($search) {
                if (!empty($search)) {
                    $query->where('content', 'like', '%' . $search . '%');
                }
            },
            'responses.sender',
            'responses.attachments'
        ]);

        return $report;
    }

    /**
     * Delete report
     *
     * @param Report $report
     *
     * @return bool
     *
     * @throws Exception
     */
    public function delete(Report $report): bool
    {
        DB::beginTransaction();
        try {
            // Delete all responses and their attachments
            foreach ($report->responses as $response) {
                if ($response->attachments->count() > 0) {
                    foreach ($response->attachments as $attachment) {
                        if (Storage::disk($attachment->disk)->exists($attachment->path)) {
                            Storage::disk($attachment->disk)->delete($attachment->path);
                        }
                    }
                }
                $response->delete();
            }

            $result = $report->delete();

            DB::commit();

            return $result;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }
}
