<?php

namespace App\Http\Controllers\Api\SubCategory;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\Api\SubCategory\SubCategoryResource;
use App\Models\Category;
use App\Models\Setting;
use App\Models\SubCategory;
use App\Services\SubCategory\SubCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defines the sub category controller for api
 */
class SubCategoryController extends BaseApiController
{
    /**
     * Call the service
     */
    public function __construct(protected SubCategoryService $service) {}

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
        $subCategories = $this->service->index($category, $request->input('limit', Setting::PAGE_RESULT_LIMIT));

        return $this->jsonSuccess(SubCategoryResource::collection($subCategories));
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
        $subCategory->load(['image']);

        return $this->jsonSuccess(SubCategoryResource::make($subCategory));
    }
}
