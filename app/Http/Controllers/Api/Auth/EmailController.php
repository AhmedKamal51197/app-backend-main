<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

/**
 * Class EmailController
 *
 * This controller handles sending Otp codes via email.
 */
class EmailController extends BaseApiController
{
    /**
     * Send the email verification notification to the authenticated user.
     *
     * @return JsonResponse
     */
    public function send(): JsonResponse
    {
        // Get the authenticated user
        $user = auth()->user();

        // Check if the user's email is already verified
        if ($user->hasVerifiedEmail()) {
            // Return an error response if the email is already verified
            return $this->jsonError(__('Email is already verified'));
        }

        // Send the email verification notification
        $user->sendEmailVerificationNotification();

        // Return a success response
        return $this->jsonSuccess([], __('Verification email has been sent successfully'));
    }

    /**
     * Verify user email.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verify(Request $request): JsonResponse
    {
        $user = request()->user();

        if ($user->hasVerifiedEmail()) {
            return $this->jsonError(__('Email is already verified'));
        }

        if(!URL::hasValidSignature($request)){
            return $this->jsonError(__('Error while verifying your email address'));
        }

        $user->markEmailAsVerified();

        return $this->jsonSuccess([], __('Email verified successfully'));
    }
}
