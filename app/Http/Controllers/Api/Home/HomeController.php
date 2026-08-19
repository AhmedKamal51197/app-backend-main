<?php

namespace App\Http\Controllers\Api\Home;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\Api\Service\ServiceResource;
use App\Http\Resources\Api\User\UserMiniResource;
use App\Models\Setting;
use App\Services\Home\HomeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * A class defines the home controller
 */
class HomeController extends BaseApiController
{
    /**
     * Load the service
     *
     * @param HomeService $service
     */
    public function __construct(protected HomeService $service)
    {
    }

    /**
     * List of the one time services
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function oneTimeServices(Request $request): JsonResponse
    {
        $services = $this->service->oneTimeServices($request->input('limit', Setting::PAGE_RESULT_LIMIT));

        return $this->jsonSuccess(ServiceResource::collection($services));
    }

    /**
     * List of the part-time services
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function partTimeServices(Request $request): JsonResponse
    {
        $services = $this->service->partTimeServices($request->input('limit', Setting::PAGE_RESULT_LIMIT));

        return $this->jsonSuccess(ServiceResource::collection($services));
    }

    /**
     * List of the users
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function users(Request $request): JsonResponse
    {
        $users = $this->service->users($request->input('limit', Setting::PAGE_RESULT_LIMIT));

        return $this->jsonSuccess(UserMiniResource::collection($users));
    }
}
