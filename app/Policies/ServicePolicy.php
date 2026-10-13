<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * A class defines the Service policy
 */
class ServicePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user owns the service.
     *
     * @param User $user
     * @param Service $service
     *
     * @return bool
     */
    public function own(User $user, Service $service): bool
    {
        return $service->user_id === $user->id;
    }
}
