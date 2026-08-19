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
 * A class defines the release order controller
 */
class ReleaseOrderController extends BaseApiController
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
     * Order release
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
            OrderStatusEnum::REVISION->value,
            OrderStatusEnum::RELEASE_PENDING->value,
        ];

        if (in_array($order->getAttribute('status'), $allowedStatuses, true)) {
            if ($this->service->isSeeker(request()->user(), $order)) {
                $this->service->release($order);

            } else {
                return $this->jsonError(__('This action allowed for seeker'));
            }
        } else {
            return $this->jsonError(__('You cant release this order'));
        }

        return $this->jsonSuccess(OrderResource::make($order->load(['category', 'subCategory']), __('This order released successfully')));
    }
}
