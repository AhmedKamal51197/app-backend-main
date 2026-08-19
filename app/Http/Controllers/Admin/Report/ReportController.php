<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Requests\Admin\Report\StoreReportRequest;
use App\Http\Resources\Admin\Report\ReportResource;
use App\Models\Report;
use App\Models\Setting;
use App\Services\Report\ReportService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defines the report controller
 */
class ReportController extends BaseAdminController
{
    /**
     * Call the service
     *
     * @param ReportService $service
     */
    public function __construct(protected ReportService $service)
    {
    }

    /**
     * List of the system reports
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $reports = $this->service->index(
            $request->input('limit', Setting::PAGE_RESULT_LIMIT),
            [
                'status' => $request->input('status', ''),
                'role' => $request->input('role', ''),
                'search' => $request->input('search', ''),
                'orderBy' => $request->input('orderBy', 'latest'),
            ]
        );

        return $this->jsonSuccess(ReportResource::collection($reports));
    }

    /**
     * Store report for a user
     *
     * @param StoreReportRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function store(StoreReportRequest $request): JsonResponse
    {
        $report = $this->service->storeForUser($request->validated());

        return $this->jsonSuccess(ReportResource::make($report), __('Report created successfully'));
    }

    /**
     * Show report with responses
     *
     * @param Request $request
     * 
     * @param Report $report
     *
     * @return JsonResponse
     */
    public function show(Request $request, Report $report): JsonResponse
    {
        
        $report = $this->service->show($report, $request->input('search', ''));

        return $this->jsonSuccess(ReportResource::make($report));
    }

    /**
     * Toggle report status (open/close)
     *
     * @param Report $report
     *
     * @return JsonResponse
     */
    public function toggleStatus(Report $report): JsonResponse
    {
        $report = $this->service->updateStatus($report);

        return $this->jsonSuccess(ReportResource::make($report), __('Report status updated'));
    }


    /**
     * Delete report (admin can delete any report)
     *
     * @param Report $report
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(Report $report): JsonResponse
    {
        $this->service->delete($report);

        return $this->jsonSuccess(null, __('Report deleted successfully'));
    }
}
