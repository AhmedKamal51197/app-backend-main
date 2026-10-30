<?php

namespace App\Policies;

use App\Models\Job;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * A class defines the Job policy
 */
class JobPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user owns the job.
     *
     * @param User $user
     * @param Job $job
     *
     * @return bool
     */
    public function own(User $user, Job $job): bool
    {
        return $job->user_id === $user->id;
    }
}
