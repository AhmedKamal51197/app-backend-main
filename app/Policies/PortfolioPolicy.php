<?php

namespace App\Policies;

use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * A class defines the Portfolio policy
 */
class PortfolioPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user owns the Portfolio
     *
     * @param User $user
     * @param Portfolio $portfolio
     *
     * @return bool
     */
    public function own(User $user, Portfolio $portfolio): bool
    {
        return $portfolio->user_id === $user->id;
    }
}
