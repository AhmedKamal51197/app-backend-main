<?php

namespace App\Policies;

use App\Models\Proposal;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * A class defines the Proposal policy
 */
class ProposalPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user may view the proposal: either the freelancer
     * who submitted it or the owner of the project it was submitted to.
     *
     * @param User $user
     * @param Proposal $proposal
     *
     * @return bool
     */
    public function own(User $user, Proposal $proposal): bool
    {
        return $proposal->user_id === $user->id
            || $proposal->project?->user_id === $user->id;
    }
}
