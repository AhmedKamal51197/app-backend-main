<?php

namespace App\Http\Controllers\Admin\Color;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Requests\Admin\Color\StoreColorRequest;
use App\Http\Requests\Admin\Color\UpdateColorRequest;
use App\Http\Resources\Admin\Color\ColorResource;
use App\Models\Color;
use App\Models\Setting;
use App\Services\Color\ColorService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defines the color controller for admin
 */
class ColorController extends BaseAdminController
{
    /**
     * Load the service
     *
     * @param ColorService $service
     */
    public function __construct(protected ColorService $service)
    {
    }

    /**
     * List of colors
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $colors = $this->service->index($request->input('limit', Setting::PAGE_RESULT_LIMIT));

        return $this->jsonSuccess(ColorResource::collection($colors));
    }

    /**
     * Store new color
     *
     * @param StoreColorRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function store(StoreColorRequest $request): JsonResponse
    {
        $color = $this->service->store($request->validated());

        return $this->jsonSuccess(ColorResource::make($color), __('Color created successfully'));
    }

    /**
     * Show color
     *
     * @param Color $color
     *
     * @return JsonResponse
     */
    public function show(Color $color): JsonResponse
    {
        return $this->jsonSuccess(ColorResource::make($color));
    }

    /**
     * Update color
     *
     * @param UpdateColorRequest $request
     * @param Color $color
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function update(UpdateColorRequest $request, Color $color): JsonResponse
    {
        $color = $this->service->update($color, $request->validated());

        return $this->jsonSuccess(ColorResource::make($color), __('Color updated successfully'));
    }

    /**
     * Delete color
     *
     * @param Color $color
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Color $color): JsonResponse
    {
        $this->service->delete($color);

        return $this->jsonSuccess([], __('Color deleted successfully'));
    }
}
