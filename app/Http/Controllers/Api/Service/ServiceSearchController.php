<?php

namespace App\Http\Controllers\Api\Service;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\Service\ServiceSearchRequest;
use App\Http\Resources\Api\Service\ServicesResource;
use App\Models\Setting;
use App\Services\Search\SearchService;
use Illuminate\Http\JsonResponse;

/**
 * A class defines the search service controller
 */
class ServiceSearchController extends BaseApiController
{
    /**
     * Load the service
     *
     * @param SearchService $service
     */
    public function __construct(protected SearchService $service)
    {
    }

    /**
     * Adding function to filter the services
     *
     * @param ServiceSearchRequest $request
     *
     * @return JsonResponse
     */
    public function __invoke(ServiceSearchRequest $request): JsonResponse
    {
        $services = $this->service->searchServices($request->validated(), $request->input('limit', Setting::PAGE_RESULT_LIMIT));

        return $this->jsonSuccess(ServicesResource::make($services));
    }
}
