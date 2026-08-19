<?php

namespace App\Http\Controllers\Api\Report;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\Report\StoreReportRequest;
use App\Http\Resources\Api\Report\ReportResource;
use App\Models\Report;
use App\Models\Setting;
use App\Services\Report\ReportService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


/**
 * A class defines the report controller
 */
class ReportController extends BaseApiController
{
    /**
     * Load report service
     *
     * @param ReportService $service
     */
    public function __construct(protected ReportService $service)
    {
    }

    /**
     * Get user reports list
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {

        $reports = $this->service->userReports(
            $request->user(),
            $request->input('limit', Setting::PAGE_RESULT_LIMIT),
            $request->input('status', ''),
        );

        return $this->jsonSuccess(ReportResource::collection($reports));
    }

    /**
     * Show report with responses
     *
     * @param Report $report
     *
     * @return JsonResponse
     */
    public function show(Report $report): JsonResponse
    {
        $this->authorize('own', $report);

        $report->load(['user', 'responses.sender', 'responses.attachments']);

        return $this->jsonSuccess(ReportResource::make($report));
    }

    /**
     * Store report
     *
     * @param StoreReportRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function store(StoreReportRequest $request): JsonResponse
    {
        $report = $this->service->store(request()->user(), $request->validated());

        return $this->jsonSuccess(new ReportResource($report), __('Report submitted successfully'));
    }

    /**
     * Close report (user can close their own report)
     *
     * @param Report $report
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function close(Report $report): JsonResponse
    {
        $this->authorize('own', $report);

        $report = $this->service->closeReport($report);

        return $this->jsonSuccess(ReportResource::make($report), __('Report closed successfully'));
    }


    /**
     * Delete report (user can delete their own report)
     *
     * @param Report $report
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(Report $report): JsonResponse
    {
        $this->authorize('own', $report);

        $this->service->delete($report);

        return $this->jsonSuccess(null, __('Report deleted successfully'));
    }
}
