<?php

namespace App\Http\Controllers\Api\Order;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\Order\RateOrderRequest;
use App\Http\Resources\Api\Order\OrderResource;
use App\Models\Order;
use App\Services\Order\OrderService;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * A class defines the rate order controller
 */
class RateOrderController extends BaseApiController
{
    /**
     * Load the service
     *
     * @param OrderService $service
     */
    public function __construct(protected OrderService $service)
    {
    }

    /**
     * Order rate
     *
     * @param Order $order
     * @param RateOrderRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function __invoke(Order $order, RateOrderRequest $request): JsonResponse
    {
        $allowedStatuses = [
            OrderStatusEnum::RELEASED->value,
            OrderStatusEnum::COMPLETED->value,
        ];

        if (in_array($order->getAttribute('status'), $allowedStatuses, true)) {
            $user = request()->user();
            
            if ($this->service->isSeeker($user, $order) || $this->service->isProvider($user, $order)) {
                $this->service->rate($order, $request->validated());
            } else {
                return $this->jsonError(__('This action allowed for seeker and provider'));
            }
        } else {
            return $this->jsonError(__('You cant rate this order'));
        }

        return $this->jsonSuccess(OrderResource::make($order->load(['category', 'subCategory']), __('This order rated successfully')));
    }
}
