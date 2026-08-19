<?php

namespace App\Http\Controllers\Api\Order;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\Order\RequestReleaseOrderRequest;
use App\Http\Resources\Api\Order\OrderResource;
use App\Models\Order;
use App\Services\Order\OrderService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * A class defines the request revision order controller
 */
class RequestReleaseOrderController extends BaseApiController
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
     * @param Order $order
     * @param RequestReleaseOrderRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function __invoke(Order $order, RequestReleaseOrderRequest $request): JsonResponse
    {
        $allowedStatuses = [
            OrderStatusEnum::REVISION->value,
            OrderStatusEnum::IN_PROGRESS->value,
        ];

        if (in_array($order->getAttribute('status'), $allowedStatuses, true)) {
            if ($this->service->isProvider(request()->user(), $order)) {
                $this->service->requestRelease($order, $request->validated());

            } else {
                return $this->jsonError(__('This action allowed for provider'));
            }
        } else {
            return $this->jsonError(__('You cant request release this order'));
        }
Log::info($order->getAttribute('status'));
        return $this->jsonSuccess(OrderResource::make($order->load(['category', 'subCategory']), __('This order release requested successfully')));
    }
}
