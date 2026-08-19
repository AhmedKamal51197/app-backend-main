<?php

namespace App\Services\Project;

use App\Actions\Categories\CategoriesAveragePricesAction;
use App\Enums\OrderStatusEnum;
use App\Enums\ProjectStatusEnum;
use App\Enums\ProposalStatusEnum;
use App\Events\LogExceptionEvent;
use App\Models\Commission;
use App\Models\Order;
use App\Models\Project;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * A class defines the admin project service
 */
class AdminProjectService
{
    public function __construct(
        private CategoriesAveragePricesAction $categoriesAction
    ) {}

    /**
     * List projects with analytics
     *
     * @param string $search
     * @param int $limit
     * @param int $page
     * @param string $status
     * @param string $orderBy
     * @param string $projectsCategoriesRate
     *
     * @return array
     */
    public function index(string $search, int $limit, int $page, string $status, string $orderBy, string $projectsCategoriesRate): array
    {
        $query = Project::with([
            'user.userSkills.skill',
            'category',
            'subCategory',
            'selectedProposal.user.userSkills.skill',
        ])->whereNot('status', ProjectStatusEnum::DRAFT->value)
            ->withCount('proposals');

        if (!empty($search)) {
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        $query->orderBy('created_at', $orderBy === 'oldest' ? 'asc' : 'desc');
        $projects = $query->paginate($limit, ['*'], 'page', $page);

        return [
            'projects' => $projects,
            'analytics' => $this->getAnalytics($projectsCategoriesRate),
            'meta' => [
                'total_pages' => $projects->lastPage(),
                'current_page' => $projects->currentPage(),
                'total_items' => $projects->total(),
                'per_page' => $projects->perPage()
            ],
        ];
    }

    /**
     * Get projects analytics
     *
     * @param string $projectsCategoriesRate
     * @return array
     */
    public function getAnalytics(string $projectsCategoriesRate): array
    {
        $allTimeQuery = Project::query();
        $completedProjects = (clone $allTimeQuery)->where('status', ProjectStatusEnum::COMPLETED->value)->count();
        $cancelledProjects = (clone $allTimeQuery)->where('status', ProjectStatusEnum::CANCELLED->value)->count();
        $totalProjects = $completedProjects + $cancelledProjects;

        return [
            'total_projects' => (clone $allTimeQuery)->count(),
            'active_projects' => (clone $allTimeQuery)->where('status', ProjectStatusEnum::IN_PROGRESS->value)->count(),
            'pending_projects' => (clone $allTimeQuery)->where('status', ProjectStatusEnum::PENDING->value)->count(),
            'completed_projects' => $completedProjects,
            'cancelled_projects' => $cancelledProjects,
            'cancel_pending_projects' => (clone $allTimeQuery)->where('status', ProjectStatusEnum::CANCEL_PENDING->value)->count(),
            'disabled_projects' => (clone $allTimeQuery)->where('status', ProjectStatusEnum::DRAFT->value)->count(),
            'disputed_projects' => (clone $allTimeQuery)->where('status', ProjectStatusEnum::DISPUTED->value)->count(),
            'refunded_projects' => (clone $allTimeQuery)->where('status', ProjectStatusEnum::REFUNDED->value)->count(),
            'complete_vs_cancelled' => $totalProjects > 0 ? round(($completedProjects / $totalProjects) * 100) : 0,
            'completion_average' => $this->getCompletionAverage(),
            'time_average' => $this->getTimeAverage($allTimeQuery),
            'price_average' => round((clone $allTimeQuery)->avg(DB::raw('(min_price + max_price) / 2')) ?? 0, 2),
            'revenue' => $this->getRevenue(),
            'profit' => $this->getProfit(),
            'projects_categories' => $this->categoriesAction->calculate(new Project, $projectsCategoriesRate),
        ];
    }

    /**
     * Get average completion time in days
     */
    private function getTimeAverage($query): int
    {
        $completedProjects = (clone $query)
            ->where('status', ProjectStatusEnum::COMPLETED->value)
            ->whereNotNull('end_date')
            ->whereNotNull('start_date')
            ->get(['start_date', 'end_date']);

        if ($completedProjects->isEmpty()) {
            return 0;
        }

        $totalDays = $completedProjects->sum(function ($project) {
            return $project->end_date->diffInDays($project->start_date);
        });

        return round($totalDays / $completedProjects->count());
    }

    /**
     * Get revenue from completed project orders
     */
    public function getRevenue(): float
    {
        return Order::whereHas('orderable', function ($query) {
            $query->where('orderable_type', Project::class);
        })
            ->where('status', OrderStatusEnum::COMPLETED->value)
            ->sum('price') ?? 0;
    }

    /**
     * Get profit from project commissions
     */
    private function getProfit(): float
    {
        return Commission::whereHasMorph('payable', [Order::class], function ($query) {
            $query->whereHas('orderable', function ($q) {
                $q->where('orderable_type', Project::class);
            });
        })->sum('amount') ?? 0;
    }

    /**
     * Get completion average percentage
     */
    private function getCompletionAverage(): int
    {
        $orderStats = Order::whereHas('orderable', function ($query) {
            $query->where('orderable_type', Project::class);
        })
            ->selectRaw('COUNT(*) as total, SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed')
            ->first();

        if (! $orderStats) {
            return 0;
        }

        $total = $orderStats->total ?? 0;
        $completed = $orderStats->completed ?? 0;

        return $total > 0 ? round(($completed / $total) * 100) : 0;
    }

    /**
     * Admin approve cancellation request
     *
     * @param Project $project
     *
     * @return Project
     *
     * @throws Exception
     */
    public function approveCancellation(Project $project): Project
    {
        if ($project->status !== ProjectStatusEnum::CANCEL_PENDING->value) {
            throw new Exception(__('Can only approve cancellation for pending cancellation projects'));
        }

        DB::beginTransaction();
        try {
            $project->update([
                'status' => ProjectStatusEnum::CANCELLED->value,
            ]);

            if ($project->selectedProposal) {
                $project->selectedProposal->update([
                    'status' => ProposalStatusEnum::CANCELLED,
                ]);
            }

            DB::commit();

            return $project->fresh(['attachments', 'proposals.attachments', 'selectedProposal.attachments', 'category', 'subCategory']);

        } catch (Exception $exception) {
            DB::rollBack();
            \event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Admin reject cancellation request
     *
     * @param Project $project
     *
     * @return Project
     *
     * @throws Exception
     */
    public function rejectCancellation(Project $project): Project
    {
        if ($project->status !== ProjectStatusEnum::CANCEL_PENDING->value) {
            throw new Exception(__('Can only reject cancellation for pending cancellation projects'));
        }

        DB::beginTransaction();
        try {
            $previousStatus = $project->selectedProposal ? ProjectStatusEnum::IN_PROGRESS->value : ProjectStatusEnum::PENDING->value;

            $project->update([
                'status' => $previousStatus,
                'cancellation_reason' => null,
                'cancelled_at' => null,
            ]);

            if ($project->selectedProposal) {
                $project->selectedProposal->update([
                    'status' => ProposalStatusEnum::IN_PROGRESS,
                ]);
            }

            DB::commit();

            return $project->fresh(['attachments', 'proposals.attachments', 'selectedProposal.attachments', 'category', 'subCategory']);

        } catch (Exception $exception) {
            DB::rollBack();
            \event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Get project details with all relations
     *
     * @param Project $project
     *
     * @return Project
     */
    public function show(Project $project): Project
    {
        $project->load([
            'user.rates',
            'user.userSkills.skill',
            'category',
            'subCategory',
            'attachments',
            'proposals.user',
            'proposals.user.userSkills.skill',
            'proposals.attachments',
            'selectedProposal.user.userSkills.skill',
            'selectedProposal.attachments',
            'rates.user',
            'rates.ratedUser',
            'chats.participants',
            'chats.messages.sender',
            'chats.messages.attachments',
            'chats.latestMessage'
        ]);

        if ($project->selectedProposal) {
            $projectOwnerId = $project->user_id;
            $proposalUserId = $project->selectedProposal->user_id;

            $chatWithSelectedProposal = $project->chats->first(function ($chat) use ($projectOwnerId, $proposalUserId) {
                $participantIds = $chat->participants->pluck('id')->toArray();

                return count($participantIds) === 2
                    && in_array($projectOwnerId, $participantIds)
                    && in_array($proposalUserId, $participantIds);
            });

            $project->setAttribute('chat_with_selected_proposal', $chatWithSelectedProposal);
        } else {
            $project->setAttribute('chat_with_selected_proposal', null);
        }

        return $project;
    }
}
