<?php

namespace App\Http\Controllers\Admin\PaymentRequest;

use App\Enums\PaymentRequestStatusEnum;
use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Resources\Admin\PaymentRequest\PaymentRequestResource;
use App\Models\PaymentRequest;
use App\Models\Setting;
use App\Services\PaymentRequest\PaymentRequestService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defines the payment request controller
 */
class PaymentRequestController extends BaseAdminController
{
    /**
     * Call the service
     *
     * @param PaymentRequestService $service
     */
    public function __construct(protected PaymentRequestService $service)
    {
    }

    /**
     * List of the system payment-requests
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->jsonSuccess(PaymentRequestResource::collection($this->service->index($request->input('limit', Setting::PAGE_RESULT_LIMIT))));
    }

    /**
     * List of pending payment-requests
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function pending(Request $request): JsonResponse
    {
        return $this->jsonSuccess(PaymentRequestResource::collection($this->service->indexByStatus(PaymentRequestStatusEnum::PENDING->value, $request->input('limit', Setting::PAGE_RESULT_LIMIT))));
    }

    /**
     * List of approved payment-requests
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function approved(Request $request): JsonResponse
    {
        return $this->jsonSuccess(PaymentRequestResource::collection($this->service->indexByStatus(PaymentRequestStatusEnum::APPROVED->value, $request->input('limit', Setting::PAGE_RESULT_LIMIT))));
    }

    /**
     * List of rejected payment-requests
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function rejected(Request $request): JsonResponse
    {
        return $this->jsonSuccess(PaymentRequestResource::collection($this->service->indexByStatus(PaymentRequestStatusEnum::REJECTED->value, $request->input('limit', Setting::PAGE_RESULT_LIMIT))));
    }

    /**
     * Show payment-request
     *
     * @param PaymentRequest $paymentRequest
     *
     * @return JsonResponse
     */
    public function show(PaymentRequest $paymentRequest): JsonResponse
    {
        $paymentRequest->load(['user', 'user.paypal', 'user.bankAccount']);

        return $this->jsonSuccess(PaymentRequestResource::make($paymentRequest));
    }

    /**
     * Approve payment-request
     *
     * @param PaymentRequest $paymentRequest
     *
     * @return JsonResponse
     *
     * @throws AuthorizationException
     * @throws Exception
     */
    public function approve(PaymentRequest $paymentRequest): JsonResponse
    {
        $this->authorize('approve', $paymentRequest);

        $this->service->accept($paymentRequest);

        return $this->jsonSuccess([], __('Payment request successfully approved'));
    }

    /**
     * Reject Payment request
     *
     * @param PaymentRequest $paymentRequest
     *
     * @return JsonResponse
     *
     * @throws AuthorizationException
     * @throws Exception
     */
    public function reject(PaymentRequest $paymentRequest): JsonResponse
    {
        $this->authorize('reject', $paymentRequest);

        $this->service->reject($paymentRequest);

        return $this->jsonSuccess([], __('Payment request successfully rejected'));
    }
}
