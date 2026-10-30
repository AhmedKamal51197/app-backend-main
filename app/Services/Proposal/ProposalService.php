<?php

namespace App\Services\Proposal;

use App\Actions\Attachments\StoreAttachmentAction;
use App\Enums\ProjectStatusEnum;
use App\Events\LogExceptionEvent;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\User;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * A class defines the proposal service
 */
class ProposalService
{
    /**
     * Get all proposals
     *
     * @param User $user
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function index(User $user, int $perPage): LengthAwarePaginator
    {
        return Proposal::query()
            ->with(['attachments'])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Store new proposal
     *
     * @param User $user
     * @param array $data
     *
     * @return Proposal
     *
     * @throws Exception
     */
    public function store(User $user, array $data): Proposal
    {
        $project = Project::where('uuid', $data['project_id'])->firstOrFail();

        $this->validateProposal($user, $project, $data);

        DB::beginTransaction();
        try {
            $proposal = Proposal::create([
                'description' => $data['description'],
                'price' => $data['price'],
                'time' => $data['time'],
                'project_id' => $project->id,
                'user_id' => $user->id,
            ]);

            if (isset($data['attachments'])) {
                StoreAttachmentAction::store($proposal, $data['attachments'], 'attachments');
            }

            DB::commit();

            return $proposal->fresh(['attachments', 'project.category', 'project.subCategory', 'project.user']);

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Validate proposal data
     *
     * @param User $user
     * @param Project $project
     * @param array $data
     *
     * @return void
     *
     * @throws Exception
     */
    private function validateProposal(User $user, Project $project, array $data): void
    {
        if (!$user->hasRole('provider')) {
            throw new Exception(__('Only service providers can submit proposals'));
        }

        if ((int) $project->user_id === (int) $user->id) {
            throw new Exception(__('You cannot submit a proposal on your own project'));
        }

        if (!$project->is_approved || $project->status !== ProjectStatusEnum::PENDING->value) {
            throw new Exception(__('This project is not open for proposals'));
        }

        $existingProposal = Proposal::where('user_id', $user->id)
            ->where('project_id', $project->id)
            ->exists();

        if ($existingProposal) {
            throw new Exception(__('You have already submitted a proposal for this project'));
        }

        $this->validateTerms($project, $data);
    }

    /**
     * Edit a proposal while the project is still open and it isn't accepted yet
     *
     * @param User $user
     * @param Proposal $proposal
     * @param array $data
     *
     * @return Proposal
     *
     * @throws Exception
     */
    public function edit(User $user, Proposal $proposal, array $data): Proposal
    {
        $project = $proposal->project;

        if ((int) $proposal->user_id !== (int) $user->id) {
            throw new Exception(__('This action is unauthorized.'));
        }

        if ($proposal->is_selected || $project->status !== ProjectStatusEnum::PENDING->value) {
            throw new Exception(__('This proposal can no longer be edited'));
        }

        $this->validateTerms($project, [
            'price' => $data['price'] ?? $proposal->price,
            'time' => $data['time'] ?? $proposal->time,
        ]);

        $proposal->update($data);

        return $proposal->fresh(['attachments', 'project.category', 'project.subCategory', 'project.user']);
    }

    /**
     * Check the proposal price and time fit the project limits
     *
     * @param Project $project
     * @param array $data
     *
     * @return void
     *
     * @throws Exception
     */
    private function validateTerms(Project $project, array $data): void
    {
        if ($data['price'] < $project->min_price) {
            throw new Exception(__('The proposal price is below the minimum required price for this project'));
        }

        if ($data['price'] > $project->max_price) {
            throw new Exception(__('The proposal price exceeds the maximum allowed price for this project'));
        }

        if ($data['time'] > $project->time) {
            throw new Exception(__('The proposal time exceeds the maximum allowed time for this project'));
        }
    }
}
