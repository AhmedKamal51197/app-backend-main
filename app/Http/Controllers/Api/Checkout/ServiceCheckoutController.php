<?php

namespace App\Http\Controllers\Api\Checkout;

use App\Events\LogExceptionEvent;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\Checkout\ServiceCheckoutRequest;
use App\Models\ServicePackage;
use App\Services\Checkout\CheckoutService;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * A class defines the Service Package checkout controller
 */
class ServiceCheckoutController extends BaseApiController
{
    /**
     * Call the service
     *
     * @param CheckoutService $service
     */
    public function __construct(protected CheckoutService $service)
    {

    }

    /**
     * Store a new checkout
     *
     * @param ServiceCheckoutRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function checkout(ServiceCheckoutRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $package = ServicePackage::where('uuid', $data['package_id'])->firstOrFail();

            $checkout = $this->service->checkout($package, request()->user(), $request->validated());

            return $this->jsonSuccess(($checkout), __('Checkout Added successfully'));

        } catch (Exception $exception) {
            event(new LogExceptionEvent($exception));

            return $this->jsonError(__('Error while creating checkout, please try again later'));
        }
    }
}


