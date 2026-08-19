<?php

namespace App\Http\Controllers\Admin\Response;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Requests\Api\Response\StoreResponseRequest;
use App\Http\Resources\Api\Response\ResponseResource;
use App\Models\Report;
use App\Services\Response\ResponseService;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * A class defines the response controller for Admin
 */
class ResponseController extends BaseAdminController
{
    /**
     * Load response service
     *
     * @param ResponseService $service
     */
    public function __construct(protected ResponseService $service)
    {
    }

    /**
     * Store response
     *
     * @param StoreResponseRequest $request
     * @param Report $report
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function store(StoreResponseRequest $request, Report $report): JsonResponse
    {
        $response = $this->service->store($report, $request->user(), $request->validated());

        return $this->jsonSuccess(ResponseResource::make($response), __('Response sent successfully'));
    }
}
