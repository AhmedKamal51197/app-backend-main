<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * A class defines the Project policy
 */
class ProjectPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user owns the project
     *
     * @param User $user
     * @param Project $project
     *
     * @return bool
     */
    public function own(User $user, Project $project): bool
    {
        return $project->user_id === $user->id;
    }
}