<?php

namespace App\Http\Controllers\Admin\Commission;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Resources\Admin\Commission\CommissionResource;
use App\Models\Commission;
use App\Models\ServicePackage;
use App\Models\Setting;
use App\Services\Commission\CommissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defines the commission controller
 */
class CommissionController extends BaseAdminController
{
    /**
     * Call the service
     *
     * @param CommissionService $service
     */
    public function __construct(protected CommissionService $service)
    {
    }

    /**
     * List of the system orders
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $data = [
            'limit' => $request->input('limit', Setting::PAGE_RESULT_LIMIT),
            'period' => $request->input('period', 'month'),
            'type' => $request->input('type'),
        ];

        $result = $this->service->index($data);

        return response()->json([
            'error' => false,
            'message' => '',
            'data' => CommissionResource::collection($result['commissions']),
            'analytics' => $result['analytics'],
            'chart' => $result['chart'],
            'meta' => $result['meta'],
        ]);
    }

    /**
     * Show Commission
     *
     * @param Commission $commission
     *
     * @return JsonResponse
     */
    public function show(Commission $commission): JsonResponse
    {
        $commission->load([
            'payable.orderable',
            'payable.seeker',
            'payable.provider'
        ]);

        if ($commission->payable && 
            $commission->payable->orderable instanceof ServicePackage) {
            $commission->payable->orderable->load('service');
        }

        return $this->jsonSuccess(CommissionResource::make($commission));
    }
}
