<?php

namespace App\Http\Controllers\Api\Order;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\Order\RequestRevisionOrderRequest;
use App\Http\Resources\Api\Order\OrderResource;
use App\Models\Order;
use App\Services\Order\OrderService;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * A class defines the request revision order controller
 */
class RequestRevisionOrderController extends BaseApiController
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
     * @param RequestRevisionOrderRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function __invoke(Order $order, RequestRevisionOrderRequest $request): JsonResponse
    {
        $allowedStatuses = [
            OrderStatusEnum::RELEASE_PENDING->value,
        ];
        if (in_array($order->getAttribute('status'), $allowedStatuses, true)) {
            if ($this->service->isSeeker(request()->user(), $order)) {
                if ($order->getAttribute('allowed_revisions') > $order->getAttribute('revisions')) {
                    $this->service->requestRevision($order, $request->validated());
                } else {
                    return $this->jsonError(__('No more revisions allowed'));
                }
            } else {
                return $this->jsonError(__('This action allowed for seeker'));
            }
        } else {
            return $this->jsonError(__('You cant revision this order wait provider to submit'));
        }

        return $this->jsonSuccess(OrderResource::make($order->load(['category', 'subCategory']), __('This order revision requested successfully')));
    }
}
