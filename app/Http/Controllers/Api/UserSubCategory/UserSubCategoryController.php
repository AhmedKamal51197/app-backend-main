<?php

namespace App\Http\Controllers\Api\UserSubCategory;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Requests\Api\UserSubCategory\RemoveUserSubCategoryRequest;
use App\Http\Requests\Api\UserSubCategory\StoreUserSubCategoriesRequest;
use App\Http\Resources\Api\User\UserResource;
use App\Services\UserSubCategory\UserSubCategoryService;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * A class defined for the user sub category controller
 */
class UserSubCategoryController extends BaseAdminController
{
    /**
     * Load the service
     *
     * @param UserSubCategoryService $service
     */
    public function __construct(protected UserSubCategoryService $service)
    {
    }

    /**
     * Index store user sub categories
     *
     * @param StoreUserSubCategoriesRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function storeUserSubCategories(StoreUserSubCategoriesRequest $request): JsonResponse
    {
        $user = $this->service->storeUserSubCategories(request()->user(), $request->validated());

        return $this->jsonSuccess(UserResource::make($user));
    }

    /**
     * Remove user sub categories
     *
     * @param RemoveUserSubCategoryRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function removeUserSubCategories(RemoveUserSubCategoryRequest $request): JsonResponse
    {
        $user = $this->service->removeUserSubCategories(request()->user(), $request->validated());

        return $this->jsonSuccess(UserResource::make($user));
    }
}
