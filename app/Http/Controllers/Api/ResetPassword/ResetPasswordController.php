<?php

namespace App\Http\Controllers\Api\ResetPassword;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\ResetPassword\ResetPasswordRequest;
use App\Http\Requests\Api\ResetPassword\SendResetPasswordOtpRequest;
use App\Http\Requests\Api\ResetPassword\VerifyResetPasswordOtpRequest;
use App\Services\EmailVerification\EmailVerificationService;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * Reset Password Controller
 */
class ResetPasswordController extends BaseApiController
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
     * Send Otp Verification
     *
     * @param SendResetPasswordOtpRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function sendOtp(SendResetPasswordOtpRequest $request): JsonResponse
    {
        $user = User::where('email', $request->input('email'))->first();

        if (!$user) {
            return $this->jsonError(__('No user found with this email'));
        }

        // Check if there's already a valid (non-expired) OTP
        if ($user->email_verification_otp &&
            $user->email_verification_otp_expires_at &&
            Carbon::now()->isBefore($user->email_verification_otp_expires_at)) {

            return $this->jsonError(__('An OTP is already active. Please wait for it to expire or use the current OTP'));
        }

        $this->service->sendVerification($user);

        return $this->jsonSuccess([], __('OTP sent to your email'));
    }

    /**
     * Verify Otp is success
     *
     * @param VerifyResetPasswordOtpRequest $request
     *
     * @return JsonResponse
     */
    public function verifyOtp(VerifyResetPasswordOtpRequest $request): JsonResponse
    {
        $user = User::where('email', $request->input('email'))->first();

        if (!$user) {
            return $this->jsonError(__('No user found with this email'));
        }

        if (!($this->service->verifyOtp($user, $request->input('otp'), true))) {
            return $this->jsonError(__('OTP is not valid'));
        }

        return $this->jsonSuccess([], __('OTP verified successfully'));
    }

    /**
     * Reset password
     *
     * @param ResetPasswordRequest $request
     *
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $user = User::where('email', $request->input('email'))->first();

        if (!($this->service->verifyOtp($user, $request->input('otp'), true))) {
            return $this->jsonError(__('OTP is not valid'));
        };

        $user->update([
            'password' => bcrypt($request->input('password')),
            'email_verification_otp' => null,
            'email_verification_otp_expires_at' => null,
        ]);

        return $this->jsonSuccess([], __('Password reset successfully'));
    }
}
