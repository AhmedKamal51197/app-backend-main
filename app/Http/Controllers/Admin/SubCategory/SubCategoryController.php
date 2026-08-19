<?php

namespace App\Http\Controllers\Admin\SubCategory;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Requests\Admin\SubCategory\EditSubCategoryRequest;
use App\Http\Requests\Admin\SubCategory\StoreSubCategoryRequest;
use App\Http\Resources\Admin\SubCategory\SubCategoryResource;
use App\Models\Category;
use App\Models\Setting;
use App\Models\SubCategory;
use App\Services\SubCategory\SubCategoryService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defines the sub category controller for admin
 */
class SubCategoryController extends BaseAdminController
{
    /**
     * Call the service
     *
     * @param SubCategoryService $service
     */
    public function __construct(protected SubCategoryService $service)
    {
    }

    /**
     * List of sub category
     *
     * @param Category $category
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Category $category, Request $request): JsonResponse
    {
        $categories = $this->service->index($category, $request->input('limit', Setting::PAGE_RESULT_LIMIT));

        return $this->jsonSuccess(SubCategoryResource::collection($categories));
    }

    /**
     * Show sub category
     *
     * @param SubCategory $subCategory
     *
     * @return JsonResponse
     */
    public function show(SubCategory $subCategory): JsonResponse
    {
        $subCategory->load(['category']);

        return $this->jsonSuccess(SubCategoryResource::make($subCategory));
    }

    /**
     * Store sub category
     *
     * @param Category $category
     * @param StoreSubCategoryRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function store(Category $category, StoreSubCategoryRequest $request): JsonResponse
    {
        $subCategory = $this->service->store($category, $request->validated());

        return $this->jsonSuccess(
            SubCategoryResource::make($subCategory),
            __('Sub Category created successfully')
        );
    }

    /**
     * Update sub category
     *
     * @param EditSubCategoryRequest $request
     * @param SubCategory $subCategory
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function edit(EditSubCategoryRequest $request, SubCategory $subCategory): JsonResponse
    {
        $category = $this->service->update($subCategory, $request->validated());

        return $this->jsonSuccess(
            SubCategoryResource::make($category),
            __('Sub Category updated successfully')
        );
    }

    /**
     * Delete Sub Category
     *
     * @param SubCategory $subCategory
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function delete(SubCategory $subCategory): JsonResponse
    {
        $this->service->delete($subCategory);

        return $this->jsonSuccess([], __('Sub Category deleted successfully'));
    }
}
