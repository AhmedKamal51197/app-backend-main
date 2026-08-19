<?php

namespace App\Http\Controllers\Api\Auth;

use App\Actions\User\CreateUserAction;
use App\Events\LogExceptionEvent;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Services\User\UserAuthenticationService;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;

/**
 * A controller class defines the registration actions
 */
class RegisterController extends BaseApiController
{
    /**
     * Register a new user
     *
     * @param RegisterRequest $request
     *
     * @return JsonResponse
     */
    public function __invoke(RegisterRequest $request): JsonResponse
    {
        try {
            // Create the user
            $user = CreateUserAction::create(
                $request->only('name', 'email', 'password', 'country_id', 'mobile', 'role', 'fcm_token')
            );
        } catch (Exception $exception) {
            event(new LogExceptionEvent($exception));
            return $this->jsonError(__('Error while registration, please try again later'));
        }

        // Trigger event to send welcome email or any other related actions
        event(new Registered($user));

        // Get the user token
        $userToken = UserAuthenticationService::getToken($user);

        return $this->jsonSuccess($userToken);
    }
}
