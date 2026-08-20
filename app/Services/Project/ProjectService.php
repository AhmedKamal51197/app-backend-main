<?php

namespace App\Services\Project;

use App\Actions\Attachments\StoreAttachmentAction;
use App\Enums\ProjectStatusEnum;
use App\Enums\ProposalStatusEnum;
use App\Events\LogExceptionEvent;
use App\Models\Category;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\SubCategory;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use App\Models\User;

/**
 * A class defines the project service
 */
class ProjectService
{
    /**
     * Get projects with unified logic
     *
     * @param string $search
     * @param int $limit
     * @param int $page
     * @param string $status
     * @param string $userId
     * @param bool $isUserProjects
     *
     * @return LengthAwarePaginator
     */
    public function index(string $search, int $limit, int $page, string $status, string $userId, bool $isUserProjects = false): LengthAwarePaginator
    {
        $query = Project::query()->with(['user', 'category', 'subCategory', 'attachments', 'selectedProposal.user', 'rates']);

        if ($isUserProjects) {
            $query->where('user_id', $userId);
        } else {
            $query->whereNotIn('status', [ProjectStatusEnum::CANCELLED->value, ProjectStatusEnum::DRAFT->value])
                  ->where('is_approved', true);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }


        if (!empty($userId) && !$isUserProjects) {
            $query->where('user_id', $userId);
        }

        return $query->orderByDesc('created_at')
            ->paginate($limit, ['*'], 'page', $page);
    }

    /**
     * Store new project
     *
     * @param User $user
     * @param array $data
     *
     * @return Project
     *
     * @throws Exception
     */
    public function store(User $user, array $data): Project
    {
        DB::beginTransaction();
        try {
            $project = Project::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'min_price' => $data['min_price'],
                'max_price' => $data['max_price'],
                'time' => $data['time'],
                'user_id' => $user->id,
                'category_id' => Category::where('uuid', $data['category_id'])->value('id'),
                'sub_category_id' => isset($data['sub_category_id']) ? SubCategory::where('uuid', $data['sub_category_id'])->value('id') : null,
                'is_approved' => false,
            ]);

            if (isset($data['attachments'])) {
                StoreAttachmentAction::store($project, $data['attachments'], 'attachments');
            }

            DB::commit();

            return $project->fresh(['attachments', 'user', 'category', 'subCategory']);

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Publish project
     *
     * @param Project $project
     *
     * @return Project
     *
     * @throws Exception
     */
    public function publish(Project $project): Project
    {
        if ($project->status !== ProjectStatusEnum::DRAFT->value) {
            throw new Exception(__('Can only publish draft project'));
        }

        try {
            $project->update([
                'status' => ProjectStatusEnum::PENDING
            ]);

            return $project->fresh(['attachments', 'proposals.attachments', 'selectedProposal.attachments', 'category', 'subCategory']);

        } catch (Exception $exception) {
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Cancel project
     *
     * @param Project $project
     * @param array $data
     *
     * @return Project
     *
     * @throws Exception
     */
    public function cancel(Project $project, array $data, bool $checkStatus = true): Project
    {
        if ($checkStatus && $project->status !== ProjectStatusEnum::PENDING->value) {
            throw new Exception(__('Can only cancel pending projects'));
        }

        DB::beginTransaction();
        try {
            $project->update([
                'status' => ProjectStatusEnum::CANCEL_PENDING->value,
                'cancellation_reason' => $data['cancellation_reason'],
                'cancelled_at' => now()
            ]);

            if ($project->selectedProposal) {
                $project->selectedProposal->update([
                    'status' => ProposalStatusEnum::CANCEL_PENDING
                ]);
            }

            DB::commit();

            return $project->fresh(['attachments', 'proposals.attachments', 'selectedProposal.attachments', 'category', 'subCategory']);

        }catch (Exception $exception){
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Complete project
     *
     * @param Project $project
     *
     * @return Project
     *
     * @throws Exception
     */
    public function complete(Project $project): Project
    {
        if ($project->status !==  ProjectStatusEnum::IN_PROGRESS->value) {
            throw new Exception(__('Can only complete in-progress projects'));
        }

        DB::beginTransaction();
        try {
            $project->update([
                'status' => ProjectStatusEnum::COMPLETED->value,
                'completed_at' => now()
            ]);

            $project->selectedProposal->update([
                'status' => ProposalStatusEnum::COMPLETED
            ]);

            DB::commit();

            return $project->fresh(['attachments', 'proposals.attachments', 'selectedProposal.attachments', 'category', 'subCategory']);

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Accept proposal for project
     *
     * @param Project $project
     * @param Proposal $proposal
     *
     * @return Project
     *
     * @throws Exception
     */
    public function acceptProposal(Project $project, Proposal $proposal): Project
    {
        if ($project->status !== ProjectStatusEnum::PENDING->value) {
            throw new Exception(__('Can only accept proposals for pending projects'));
        }

        if ($project->selectedProposal){
            throw new Exception(__('Already there is an accepted proposal'));
        }

        if ($proposal->project_id !== $project->id) {
            throw new Exception(__('Proposal does not belong to this project'));
        }

        DB::beginTransaction();
        try {
            $project->update([
                'selected_proposal_id' => $proposal->id
            ]);

            $proposal->update([
                'is_selected' => true
            ]);

            DB::commit();

            return $project->fresh(['attachments', 'proposals.attachments', 'selectedProposal.attachments', 'category', 'subCategory']);

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Update project status when order is approved
     *
     * @param Project $project
     *
     * @return void
     *
     * @throws Exception
     */
    public function updateStatusWhenApproved(Project $project): void
    {
        DB::beginTransaction();
        try {
            $project->update([
                'status' => ProjectStatusEnum::IN_PROGRESS->value,
                'started_at' => now()
            ]);

            if ($project->selectedProposal) {
                $project->selectedProposal->update([
                    'status' => ProposalStatusEnum::IN_PROGRESS->value
                ]);
            }

            DB::commit();

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Update project status when order is rejected
     *
     * @param Project $project
     *
     * @return void
     *
     * @throws Exception
     */
    public function updateStatusWhenRejected(Project $project): void
    {
        DB::beginTransaction();
        try {
            $project->update([
                'status' => ProjectStatusEnum::CANCEL_PENDING->value,
                'cancellation_reason' => __('Order was rejected'),
                'cancelled_at' => now()
            ]);

            if ($project->selectedProposal) {
                $project->selectedProposal->update([
                    'status' => ProposalStatusEnum::CANCELLED
                ]);
            }

            DB::commit();

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }
}
