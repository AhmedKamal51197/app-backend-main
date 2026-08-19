<?php

namespace App\Http\Controllers\Api\Favorite;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\Api\Favorite\FavoriteServiceResource;
use App\Models\Service;
use App\Models\Setting;
use App\Services\Favorite\FavoriteServiceService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defines the favorite service controller
 */
class FavoriteServiceController extends BaseApiController
{
    /**
     * Load the service
     *
     * @param FavoriteServiceService $service
     */
    public function __construct(protected FavoriteServiceService $service)
    {
    }

    /**
     * Get user's favorite services
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $favorites = $this->service->index(request()->user(), $request->input('limit', Setting::PAGE_RESULT_LIMIT));

        return $this->jsonSuccess(FavoriteServiceResource::make($favorites));
    }

    /**
     * Add service to favorites
     *
     * @param Service $service
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function store(Service $service): JsonResponse
    {
        if ($this->service->getFavorite(request()->user(), $service)) {
            return $this->jsonError(__('This service already added to favorite'));
        }
        $this->service->store(request()->user(), $service);

        return $this->jsonSuccess([], __('Added to favorite successfully'));
    }

    /**
     * Remove service from favorites
     *
     * @param Service $service
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function delete(Service $service): JsonResponse
    {
        if (!($this->service->getFavorite(request()->user(), $service))) {
            return $this->jsonError(__('This service is not in favorite'));
        }

        $this->service->delete(request()->user(), $service);

        return $this->jsonSuccess([], __('Service removed from favorites successfully'));
    }
}
