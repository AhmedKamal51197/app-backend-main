<?php

namespace App\Http\Controllers\Api\Order;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\Api\Order\OrderResource;
use App\Models\Order;
use App\Services\Order\OrderService;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * A class defines the approval order controller
 */
class ApproveOrderController extends BaseApiController
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
     * Order approval
     *
     * @param Order $order
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function __invoke(Order $order): JsonResponse
    {
        if ($order->getAttribute('status') === OrderStatusEnum::APPROVAL_PENDING->value) {
            if ($this->service->isProvider(request()->user(), $order)) {
                $this->service->approve($order);

            } else {
                return $this->jsonError(__('This action allowed for provider'));
            }

        } else {
            return $this->jsonError(__('You cant approve this order'));
        }

        return $this->jsonSuccess(OrderResource::make($order->load(['category', 'subCategory'])), __('This order approved successfully'));
    }
}
