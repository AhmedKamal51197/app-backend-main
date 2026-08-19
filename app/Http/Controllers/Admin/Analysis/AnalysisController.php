<?php

namespace App\Http\Controllers\Admin\Analysis;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Services\Analysis\AnalysisService;
use Illuminate\Http\JsonResponse;

/**
 * A class defines the analysis controller for admin
 */
class AnalysisController extends BaseAdminController
{
    /**
     * Call the service
     *
     * @param AnalysisService $service
     */
    public function __construct(protected AnalysisService $service)
    {
    }

    /**
     * Analysis
     *
     * @return JsonResponse
     */
    public function analysis(): JsonResponse
    {
        return $this->jsonSuccess($this->service->analysis());
    }
}
