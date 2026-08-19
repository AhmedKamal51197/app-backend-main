<?php

namespace App\Http\Controllers\Api\PayPal;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\PayPal\StorePayPalRequest;
use App\Http\Resources\Api\PayPal\PayPalResource;
use App\Services\PayPal\PayPalService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defines the PayPal controller
 */
class PayPalController extends BaseApiController
{
    /**
     * Load service
     *
     * @param PayPalService $service
     */
    public function __construct(protected PayPalService $service)
    {
    }

    /**
     * Get user's bank account
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function userPayPal(Request $request): JsonResponse
    {
        $user = request()->user();

        if (!($user->hasPaypal())) {
            return $this->jsonError(
                __('User doesnt have a paypal')
            );
        }
        $bankAccount = $this->service->userPayPal(request()->user());

        return $this->jsonSuccess(PayPalResource::make($bankAccount));
    }

    /**
     * Store paypal data
     *
     * @param StorePayPalRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function store(StorePayPalRequest $request): JsonResponse
    {
        $user = request()->user();

        $payPal = $this->service->store($user, $request->validated());

        return $this->jsonSuccess(
            PayPalResource::make($payPal),
            __('PayPal updated successfully')
        );
    }
}
