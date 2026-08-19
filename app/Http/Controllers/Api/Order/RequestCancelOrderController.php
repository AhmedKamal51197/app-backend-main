<?php

namespace App\Http\Controllers\Api\Order;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\Order\RequestCancelOrderRequest;
use App\Http\Resources\Api\Order\OrderResource;
use App\Models\Order;
use App\Services\Order\OrderService;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * A class defines the approval order controller
 */
class RequestCancelOrderController extends BaseApiController
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
     * Order requested cancel
     *
     * @param RequestCancelOrderRequest $request
     * @param Order $order
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function __invoke(RequestCancelOrderRequest $request, Order $order): JsonResponse
    {
        $allowedStatuses = [
            OrderStatusEnum::IN_PROGRESS->value,
            OrderStatusEnum::RELEASE_PENDING->value,
            OrderStatusEnum::REVISION->value,
        ];
        if (in_array($order->getAttribute('status'), $allowedStatuses, true)) {
            if (($this->service->isSeeker(request()->user(), $order)) || $this->service->isProvider(request()->user(), $order)) {
                $this->service->requestCancel($order, $request->validated());

            } else {
                return $this->jsonError(__('This action is not allowed'));
            }

        } else {
            return $this->jsonError(__('You cant request cancel this order'));
        }

        return $this->jsonSuccess(OrderResource::make($order->load(['category', 'subCategory']), __('This order cancel requested successfully')));
    }
}
