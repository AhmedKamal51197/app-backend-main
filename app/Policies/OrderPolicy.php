<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * A class defines the Order policy
 */
class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user is a party to the order (its seeker or provider).
     *
     * @param User $user
     * @param Order $order
     *
     * @return bool
     */
    public function own(User $user, Order $order): bool
    {
        return $order->seeker_id === $user->id
            || $order->provider_id === $user->id;
    }
}
