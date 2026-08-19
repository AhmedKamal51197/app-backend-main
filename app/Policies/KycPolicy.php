<?php

namespace App\Policies;

use App\Enums\KycStatusEnum;
use App\Models\Kyc;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * A class defines the KYC policy
 */
class KycPolicy
{
    use HandlesAuthorization;

    /**
     * Authorize approve KYC
     *
     * @param User $user
     * @param Kyc $kyc
     *
     * @return bool
     */
    public function approve(User $user, Kyc $kyc): bool
    {
        if ($kyc->getAttribute('status') != KycStatusEnum::PENDING->value) {
            return false;
        }
        return true;
    }

    /**
     * Authorize reject KYC
     *
     * @param User $user
     * @param Kyc $kyc
     *
     * @return bool
     */
    public function reject(User $user, Kyc $kyc): bool
    {
        if ($kyc->getAttribute('status') != KycStatusEnum::PENDING->value) {
            return false;
        }
        return true;
    }
}
