<?php

namespace App\Services\EmailVerification;

use App\Models\User;
use Exception;

/**
 * Class EmailVerificationService
 *
 * Service class for handling Email OTP verification.
 */
class EmailVerificationService
{
    /**
     * Verify OTP
     *
     * @param User $user
     * @param string $otp
     * @param bool $reset
     *
     * @return bool
     */
    public function verifyOtp(User $user, string $otp, bool $reset): bool
    {
        return $user->verifyEmailWithOtp($otp, $reset);
    }

    /**
     * Send verification email otp
     *
     * @param User $user
     *
     * @return void
     *
     * @throws Exception
     */
    public function sendVerification(User $user): void
    {
        $user->sendEmailVerificationNotification();
    }
}
