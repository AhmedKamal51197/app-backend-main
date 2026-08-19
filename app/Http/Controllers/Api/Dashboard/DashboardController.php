<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\Dashboard\DashboardService;
use Illuminate\Http\JsonResponse;

/**
 * A class defines the dashboard controller
 */
class DashboardController extends BaseApiController
{
    /**
     * Load the service
     *
     * @param DashboardService $service
     */
    public function __construct(protected DashboardService $service)
    {
    }

    /**
     * Get dashboard analytics
     *
     * @return JsonResponse
     */
    public function analytics(): JsonResponse
    {
        $analytics = $this->service->getDashboardAnalytics(request()->user());

        return $this->jsonSuccess($analytics);
    }
}
