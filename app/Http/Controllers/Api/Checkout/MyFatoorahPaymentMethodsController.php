<?php

namespace App\Http\Controllers\Api\Checkout;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\Payment\MyFatoorahService;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * A class defines myfatoorah payment method controller
 */
class MyFatoorahPaymentMethodsController extends BaseApiController
{
    /**
     * Call the service
     *
     * @param MyFatoorahService $service
     */
    public function __construct(protected MyFatoorahService $service)
    {

    }

    /**
     * Store a new checkout
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function checkout(): JsonResponse
    {
        return $this->jsonSuccess($this->service->getPaymentMethods());
    }
}


