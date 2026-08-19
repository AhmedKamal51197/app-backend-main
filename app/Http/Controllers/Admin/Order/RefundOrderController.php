<?php

namespace App\Http\Controllers\Admin\Order;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Resources\Admin\Order\OrderResource;
use App\Models\Order;
use App\Services\Order\AdminOrderService;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * A class defines the refund order controller
 */
class RefundOrderController extends BaseAdminController
{
    /**
     * Load the service
     *
     * @param AdminOrderService $service
     */
    public function __construct(protected AdminOrderService $service)
    {
    }

    /**
     * Order requested cancel
     *
     * @param Order $order
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function __invoke(Order $order): JsonResponse
    {
        $allowedStatuses = [
            OrderStatusEnum::CANCELLED->value,
        ];
        if (in_array($order->getAttribute('status'), $allowedStatuses, true)) {
            $this->service->refund(request()->user(), $order);

        } else {
            return $this->jsonError(__('You cant refund this order'));
        }

        return $this->jsonSuccess(OrderResource::make($order));
    }
}
