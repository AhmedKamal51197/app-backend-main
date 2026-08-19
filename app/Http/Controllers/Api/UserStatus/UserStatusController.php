<?php

namespace App\Http\Controllers\Api\UserStatus;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Requests\Api\UserStatus\EditUserStatusRequest;
use App\Http\Resources\Api\User\UserResource;
use App\Services\UserStatus\UserStatusService;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * A class defined for the user status controller
 */
class UserStatusController extends BaseAdminController
{
    /**
     * Load the service
     *
     * @param UserStatusService $service
     */
    public function __construct(protected UserStatusService $service)
    {
    }

    /**
     * Change user status
     *
     * @param EditUserStatusRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function changeStatus(EditUserStatusRequest $request): JsonResponse
    {
        $user = $this->service->changeStatus(request()->user(), $request->validated());

        return $this->jsonSuccess(UserResource::make($user));
    }
}
