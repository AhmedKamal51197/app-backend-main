<?php

namespace App\Http\Controllers\Api\Search;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\Search\SearchRequest;
use App\Http\Resources\Api\Search\SearchResource;
use App\Models\Setting;
use App\Services\Search\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defines the search controller
 */
class SearchController extends BaseApiController
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
     * Search
     *
     * @param SearchRequest $request
     * @param SearchRequest $searchRequest
     *
     * @return JsonResponse
     */
    public function search(Request $request, SearchRequest $searchRequest): JsonResponse
    {
        $data = $this->service->search($searchRequest->validated(), $request->input('limit', Setting::PAGE_RESULT_LIMIT));

        return $this->jsonSuccess(SearchResource::make($data));
    }
}
