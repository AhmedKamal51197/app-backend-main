<?php

namespace App\Http\Controllers\Api\Auth;

use App\Events\LogExceptionEvent;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Api\Auth\VerifyRegistrationRequest;
use App\Models\PendingRegistration;
use App\Services\User\RegistrationService;
use App\Services\User\UserAuthenticationService;
use Exception;
use Illuminate\Http\JsonResponse;
use RuntimeException;

/**
 * Sign-up confirmed by email OTP: no account exists until the code is right.
 *
 * 1. POST /register/start   same body as /register -> code sent to the email
 * 2. POST /register/verify  { email, otp }         -> account created + token
 *    POST /register/resend  { email }              -> new code (1 per minute)
 */
class RegistrationOtpController extends BaseApiController
{
    public function __construct(protected RegistrationService $service)
    {
    }

    public function start(RegisterRequest $request): JsonResponse
    {
        return $this->run(function () use ($request) {
            $this->service->start($request->only('name', 'email', 'password', 'country_id', 'mobile', 'role', 'fcm_token'));

            return $this->jsonSuccess($this->codeInfo(), __('OTP generated and Email sent successfully'));
        });
    }

    public function resend(VerifyRegistrationRequest $request): JsonResponse
    {
        return $this->run(function () use ($request) {
            $this->service->resend($request->input('email'));

            return $this->jsonSuccess($this->codeInfo(), __('OTP generated and Email sent successfully'));
        });
    }

    public function verify(VerifyRegistrationRequest $request): JsonResponse
    {
        return $this->run(function () use ($request) {
            $user = $this->service->verify($request->input('email'), $request->input('otp'));

            if ($request->filled('fcm_token')) {
                $user->update(['fcm_token' => $request->input('fcm_token')]);
            }

            // Same shape as /login and /register (email_verified is true).
            return $this->jsonSuccess(UserAuthenticationService::getToken($user), __('Email verified successfully'));
        });
    }

    private function codeInfo(): array
    {
        return [
            'otp_expires_in' => PendingRegistration::OTP_MINUTES * 60,
            'resend_after' => PendingRegistration::RESEND_SECONDS,
        ];
    }

    private function run(callable $action): JsonResponse
    {
        try {
            return $action();
        } catch (RuntimeException $exception) {
            $code = in_array($exception->getCode(), [404, 422, 429], true) ? $exception->getCode() : 400;

            return $this->jsonError($exception->getMessage(), $code);
        } catch (Exception $exception) {
            event(new LogExceptionEvent($exception));

            return $this->jsonError(__('Error while registration, please try again later'));
        }
    }
}
