<?php

namespace App\Http\Controllers\Admin\Category;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Requests\Admin\Category\EditCategoryRequest;
use App\Http\Requests\Admin\Category\StoreCategoryRequest;
use App\Http\Resources\Admin\Category\CategoryResource;
use App\Models\Category;
use App\Models\Setting;
use App\Services\Category\CategoryService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defines the category controller for admin
 */
class CategoryController extends BaseAdminController
{
    /**
     * Call the service
     *
     * @param CategoryService $service
     */
    public function __construct(protected CategoryService $service)
    {
    }

    /**
     * List of category
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $categories = $this->service->index($request->input('limit', Setting::PAGE_RESULT_LIMIT));

        return $this->jsonSuccess(CategoryResource::collection($categories));
    }

    /**
     * Show category
     *
     * @param Category $category
     *
     * @return JsonResponse
     */
    public function show(Category $category): JsonResponse
    {
        $category->load(['subCategories.image', 'image']);

        return $this->jsonSuccess(CategoryResource::make($category));
    }

    /**
     * Store category
     *
     * @param StoreCategoryRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $cropVariety = $this->service->store($request->validated());

        return $this->jsonSuccess(
            CategoryResource::make($cropVariety),
            __('Category created successfully')
        );
    }

    /**
     * Update category
     *
     * @param EditCategoryRequest $request
     * @param Category $category
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function edit(EditCategoryRequest $request, Category $category): JsonResponse
    {
        $category = $this->service->update($category, $request->validated());

        return $this->jsonSuccess(
            CategoryResource::make($category),
            __('Category updated successfully')
        );
    }

    /**
     * Delete Category
     *
     * @param Category $category
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function delete(Category $category): JsonResponse
    {
        $this->service->delete($category);

        return $this->jsonSuccess([], __('Category deleted successfully'));
    }
}
