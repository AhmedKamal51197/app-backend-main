<?php

namespace App\Http\Controllers\Api\PaymentRequest;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\PaymentRequest\StorePaymentRequest;
use App\Http\Resources\Api\PaymentRequest\PaymentRequestResource;
use App\Models\PaymentRequest;
use App\Models\Setting;
use App\Services\PaymentRequest\PaymentRequestService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defines the payment request controller
 */
class PaymentRequestController extends BaseApiController
{
    /**
     * Load Payment Request service
     *
     * @param PaymentRequestService $service
     */
    public function __construct(protected PaymentRequestService $service)
    {
    }

    /**
     * List of the user payment requests
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $paymentRequest = $this->service->userPaymentRequests(request()->user(), $request->input('limit', Setting::PAGE_RESULT_LIMIT));

        return $this->jsonSuccess(PaymentRequestResource::collection($paymentRequest));
    }

    /**
     * Show the Payment Request
     *
     * @param PaymentRequest $paymentRequest
     *
     * @return JsonResponse
     */
    public function show(PaymentRequest $paymentRequest): JsonResponse
    {
        $paymentRequest->load(['user']);

        return $this->jsonSuccess(PaymentRequestResource::make($paymentRequest));
    }

    /**
     * Store Payment Request data
     *
     * @param StorePaymentRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function store(StorePaymentRequest $request): JsonResponse
    {
        $user = request()->user();

        $data = $request->validated();

        if($user->walletBalance() < $data['amount']) {
            return  $this->jsonError(__('No sufficient wallet balance'));
        }

        $paymentRequest = $this->service->store($user, $request->validated());

        return $this->jsonSuccess(
            PaymentRequestResource::make($paymentRequest),
            __('Payment Request created successfully')
        );
    }
}
