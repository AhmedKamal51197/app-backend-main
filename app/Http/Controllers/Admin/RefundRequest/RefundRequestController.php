<?php

namespace App\Http\Controllers\Admin\RefundRequest;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Models\RefundRequest;
use App\Services\RefundRequest\RefundRequestService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Admin approve/reject actions on refund requests (the refunds shown in the
 * financial-flow table). Money is moved manually; these record the decision.
 */
class RefundRequestController extends BaseAdminController
{
    /**
     * Call the service
     *
     * @param RefundRequestService $service
     */
    public function __construct(protected RefundRequestService $service)
    {
    }

    /**
     * Approve a refund request.
     *
     * @param RefundRequest $refundRequest
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function approve(RefundRequest $refundRequest): JsonResponse
    {
        $this->service->approve($refundRequest);

        return $this->jsonSuccess([], __('Refund request successfully approved'));
    }

    /**
     * Reject a refund request, storing the admin's reason.
     *
     * @param Request $request
     * @param RefundRequest $refundRequest
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function reject(Request $request, RefundRequest $refundRequest): JsonResponse
    {
        $reason = $request->input('reason', $request->input('notes'));

        $this->service->reject($refundRequest, $reason);

        return $this->jsonSuccess([], __('Refund request successfully rejected'));
    }
}
