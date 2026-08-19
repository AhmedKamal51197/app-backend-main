<?php

namespace App\Http\Controllers\Admin\Feature;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Requests\Admin\Feature\StoreFeatureRequest;
use App\Http\Requests\Admin\Feature\UpdateFeatureRequest;
use App\Http\Resources\Admin\Feature\FeatureResource;
use App\Models\Feature;
use App\Models\Setting;
use App\Services\Feature\FeatureService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defines the feature controller for admin
 */
class FeatureController extends BaseAdminController
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
     * List of features
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

    /**
     * Store new feature
     *
     * @param StoreFeatureRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function store(StoreFeatureRequest $request): JsonResponse
    {
        $feature = $this->service->store($request->validated());

        return $this->jsonSuccess(FeatureResource::make($feature), __('Feature created successfully'));
    }

    /**
     * Update feature
     *
     * @param UpdateFeatureRequest $request
     * @param Feature $feature
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function update(UpdateFeatureRequest $request, Feature $feature): JsonResponse
    {
        $feature = $this->service->update($feature, $request->validated());

        return $this->jsonSuccess(FeatureResource::make($feature), __('Feature updated successfully'));
    }

    /**
     * Delete feature
     *
     * @param Feature $feature
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function delete(Feature $feature): JsonResponse
    {
        $this->service->delete($feature);

        return $this->jsonSuccess( __('Feature deleted successfully'));
    }
}
