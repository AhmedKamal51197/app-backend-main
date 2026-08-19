<?php

namespace App\Policies;

use App\Enums\PaymentRequestStatusEnum;
use App\Models\PaymentRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * A class defines the Payment-Request policy
 */
class PaymentRequestPolicy
{
    use HandlesAuthorization;

    /**
     * Authorize approve PaymentRequest
     *
     * @param User $user
     * @param PaymentRequest $paymentRequest
     *
     * @return bool
     */
    public function approve(User $user, PaymentRequest $paymentRequest): bool
    {
        if ($paymentRequest->getAttribute('status') != PaymentRequestStatusEnum::PENDING->value) {
            return false;
        }
        return true;
    }

    /**
     * Authorize reject PaymentRequest
     *
     * @param User $user
     * @param PaymentRequest $paymentRequest
     *
     * @return bool
     */
    public function reject(User $user, PaymentRequest $paymentRequest): bool
    {
        if ($paymentRequest->getAttribute('status') != PaymentRequestStatusEnum::PENDING->value) {
            return false;
        }
        return true;
    }
}
