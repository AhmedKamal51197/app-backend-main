<?php

namespace App\Services\Chat;

use App\Models\Chat;
use App\Models\Project;
use App\Models\Service;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * A class defines the admin chat service
 */
class AdminChatService
{
    /**
     * Get all chats with filters for admin
     *
     * @param array $data
     *
     * @return LengthAwarePaginator
     */
    public function index(array $data): LengthAwarePaginator
    {
        $query = Chat::query()
            ->with([
                'participants',
                'order',
                'chattable' => function ($morphTo) {
                    $morphTo->morphWith([
                        Project::class => ['category', 'subCategory', 'user'],
                        Service::class => ['category', 'subCategory', 'user'],
                    ]);
                }
            ]);

        if (!empty($data['type'])) {
                $query->where('type', $data['type']);
        }

        // Filter by project status
        if (!empty($data['project_status'])) {
            $query->whereHasMorph('chattable', [Project::class], function (Builder $q) use ($data) {
                $q->where('status', $data['project_status']);
            });
        }

        // Filter by order status
        if (!empty($data['order_status'])) {
            $query->whereHas('order', function (Builder $q) use ($data) {
                $q->where('status', $data['order_status']);
            });
        }

        // Search in chat title or chattable title
        if (!empty($data['search'])) {
            $search = $data['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHasMorph('chattable', [Project::class, Service::class], function (Builder $q) use ($search) {
                        $q->where('title', 'like', "%{$search}%");
                    });
            });
        }

        // Order by latest activity
        $query->orderByDesc('last_message_at')
            ->orderByDesc('created_at');

        return $query->paginate(
            perPage: $data['limit'] ?? Setting::PAGE_RESULT_LIMIT,
            page: $data['page'] ?? Setting::PAGE
        );
    }
}
