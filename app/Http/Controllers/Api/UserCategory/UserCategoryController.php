<?php

namespace App\Http\Controllers\Api\UserCategory;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Requests\Api\UserCategory\StoreUserCategoryRequest;
use App\Http\Resources\Api\User\UserMiniResource;
use App\Services\UserCategory\UserCategoryService;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * A class defined for the user category controller
 */
class UserCategoryController extends BaseAdminController
{
    /**
     * Load the service
     *
     * @param UserCategoryService $service
     */
    public function __construct(protected UserCategoryService $service)
    {
    }

    /**
     * Index store user category
     *
     * @param StoreUserCategoryRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function storeUserCategory(StoreUserCategoryRequest $request): JsonResponse
    {
        $user = $this->service->storeUserCategory(request()->user(), $request->validated());

        return $this->jsonSuccess(UserMiniResource::make($user));
    }
}
