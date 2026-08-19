<?php

namespace App\Http\Controllers\Api\Category;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Resources\Api\Category\CategoryResource;
use App\Models\Category;
use App\Models\Setting;
use App\Services\Category\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defined for the category controller
 */
class CategoryController extends BaseAdminController
{
    /**
     * Load the service
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
}
