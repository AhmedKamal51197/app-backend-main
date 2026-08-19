<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Services\User\UserAuthenticationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * A class defines the login controller's actions
 */
class LoginController extends BaseApiController
{
    /**
     * Login and generate token
     */
    public function __invoke(LoginRequest $request): JsonResponse
    {
        $credentials = request(['email', 'password']);

        if (!Auth::attempt($credentials)) {
            return $this->jsonError(
                message: __('Unauthorized, Incorrect email or password'),
                code: Response::HTTP_UNAUTHORIZED
            );
        }

        $user = $request->user();
        $user->update([
            'last_login_at' => now(),
            'fcm_token' => $request->fcm_token ?? $user->fcm_token
        ]);
        $userToken = UserAuthenticationService::getToken($user);

        return $this->jsonSuccess($userToken);
    }
}
