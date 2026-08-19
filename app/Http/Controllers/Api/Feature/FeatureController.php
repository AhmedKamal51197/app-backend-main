<?php

namespace App\Http\Controllers\Api\Feature;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\Api\Feature\FeatureResource;
use App\Models\Feature;
use App\Models\Setting;
use App\Services\Feature\FeatureService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defines the feature controller for user API
 */
class FeatureController extends BaseApiController
{
    /**
     * Load the service
     *
     * @param FeatureService $service
     */
    public function __construct(protected FeatureService $service)
    {
    }

    /**
     * List all features
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $features = $this->service->index($request->input('limit', Setting::PAGE_RESULT_LIMIT));

        return $this->jsonSuccess(FeatureResource::collection($features));
    }

    /**
     * Show feature
     *
     * @param Feature $feature
     *
     * @return JsonResponse
     */
    public function show(Feature $feature): JsonResponse
    {
        $feature->load(['category']);

        return $this->jsonSuccess(FeatureResource::make($feature));
    }
}
