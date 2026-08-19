<?php

namespace App\Http\Controllers\Api\UserSkill;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Requests\Api\UserSkill\StoreUserSkillsRequest;
use App\Http\Requests\Api\UserSkill\RemoveUserSkillsRequest;
use App\Http\Resources\Api\User\UserResource;
use App\Services\UserSkill\UserSkillService;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * A class defined for the user skill controller
 */
class UserSkillController extends BaseAdminController
{
    /**
     * Load the service
     *
     * @param UserSkillService $service
     */
    public function __construct(protected UserSkillService $service)
    {
    }

    /**
     * Store user skills
     *
     * @param StoreUserSkillsRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function storeUserSkills(StoreUserSkillsRequest $request): JsonResponse
    {
        $user = $this->service->storeUserSkills(request()->user(), $request->validated());

        return $this->jsonSuccess(UserResource::make($user));
    }

    /**
     * Remove user skills
     *
     * @param RemoveUserSkillsRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function removeUserSkills(RemoveUserSkillsRequest $request): JsonResponse
    {
        $user = $this->service->removeUserSkills(request()->user(), $request->validated());

        return $this->jsonSuccess(UserResource::make($user));
    }
}
