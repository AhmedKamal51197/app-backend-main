<?php

namespace App\Http\Controllers\Api\EmailVerification;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\EmailVerification\VerifyEmailVerificationRequest;
use App\Services\EmailVerification\EmailVerificationService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * A class defines the email verification controller
 */
class EmailVerificationController extends BaseApiController
{
    /**
     * Load the service
     *
     * @param EmailVerificationService $service
     */
    public function __construct(protected EmailVerificationService $service)
    {
    }

    /**
     * Verify user email
     *
     * @param VerifyEmailVerificationRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function verify(VerifyEmailVerificationRequest $request): JsonResponse
    {
        $user = request()->user();

        if ($user->getAttribute('email_verified_at')) {
            return $this->jsonError(__('Email is already verified'));
        }

        if (!($this->service->verifyOtp($user, $request->input('otp'), false))) {
            return $this->jsonError(__('OTP is not valid'));
        }

        return $this->jsonSuccess([], __('Email verified successfully'));
    }

    /**
     * send a verification code to the user email
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function send(): JsonResponse
    {
        $user = request()->user();

        if ($user->getAttribute('email_verified_at')) {
            return $this->jsonError(__('Email is already verified'));
        }

        // Check if there's already a valid (non-expired) OTP
        if ($user->email_verification_otp &&
            $user->email_verification_otp_expires_at &&
            Carbon::now()->isBefore($user->email_verification_otp_expires_at)) {

            return $this->jsonError(__('An OTP is already active. Please wait for it to expire or use the current OTP'));
        }

        $this->service->sendVerification($user);

        return $this->jsonSuccess([], __('OTP generated and Email sent successfully'));
    }
}
