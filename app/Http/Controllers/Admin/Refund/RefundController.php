<?php

namespace App\Http\Controllers\Admin\Refund;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Resources\Admin\Refund\RefundResource;
use App\Models\Refund;
use App\Models\Setting;
use App\Services\Refund\RefundService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defines the refund controller
 */
class RefundController extends BaseAdminController
{
    /**
     * Call the service
     *
     * @param RefundService $service
     */
    public function __construct(protected RefundService $service)
    {
    }

    /**
     * List of the refund
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->jsonSuccess(RefundResource::collection($this->service->index($request->input('limit', Setting::PAGE_RESULT_LIMIT))));
    }

    /**
     * Show refund
     *
     * @param Refund $refund
     *
     * @return JsonResponse
     */
    public function show(Refund $refund): JsonResponse
    {
        $refund->load(['refundable']);

        return $this->jsonSuccess(RefundResource::make($refund));
    }
}
