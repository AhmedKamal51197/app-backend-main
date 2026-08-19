<?php

namespace App\Http\Controllers\Admin\Order;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Resources\Admin\Order\OrderResource;
use App\Models\Order;
use App\Models\Setting;
use App\Services\Order\AdminOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defines the Order controller
 */
class OrderController extends BaseAdminController
{
    /**
     * Call the service
     *
     * @param AdminOrderService $service
     */
    public function __construct(protected AdminOrderService $service)
    {
    }

    /**
     * List of the system orders
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $result = $this->service->index(
            $request->input('search', ''),
            $request->input('limit', Setting::PAGE_RESULT_LIMIT),
            $request->input('page', Setting::PAGE),
            $request->input('status', ''),
            $request->input('order_by', 'latest'),
            $request->input('orders_categories_rate', 'monthly'),
        );

        return $this->jsonSuccess([
            'orders' => OrderResource::collection($result['orders'] ?? []),
            'analytics' => $result['analytics'] ?? []
        ]);
    }

    /**
     * List of pending Orders
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function pending(Request $request): JsonResponse
    {
        return $this->jsonSuccess(OrderResource::collection($this->service->indexByStatuses([OrderStatusEnum::APPROVAL_PENDING->value], $request->input('limit', Setting::PAGE_RESULT_LIMIT))));
    }


    /**
     * List of rejected Orders
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function rejected(Request $request): JsonResponse
    {
        return $this->jsonSuccess(OrderResource::collection($this->service->indexByStatuses([OrderStatusEnum::REJECTED->value], $request->input('limit', Setting::PAGE_RESULT_LIMIT))));
    }

    /**
     * List of inProgress Orders
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function inProgress(Request $request): JsonResponse
    {
        return $this->jsonSuccess(OrderResource::collection($this->service->indexByStatuses([OrderStatusEnum::IN_PROGRESS->value, OrderStatusEnum::REVISION->value, OrderStatusEnum::RELEASE_PENDING->value], $request->input('limit', Setting::PAGE_RESULT_LIMIT))));
    }

    /**
     * List of released Orders
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function released(Request $request): JsonResponse
    {
        return $this->jsonSuccess(OrderResource::collection($this->service->indexByStatuses([OrderStatusEnum::RELEASED->value], $request->input('limit', Setting::PAGE_RESULT_LIMIT))));
    }

    /**
     * List of refunded Orders
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function refunded(Request $request): JsonResponse
    {
        return $this->jsonSuccess(OrderResource::collection($this->service->indexByStatuses([OrderStatusEnum::REFUNDED->value], $request->input('limit', Setting::PAGE_RESULT_LIMIT))));
    }

    /**
     * List of completed Orders
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function completed(Request $request): JsonResponse
    {
        return $this->jsonSuccess(OrderResource::collection($this->service->indexByStatuses([OrderStatusEnum::COMPLETED->value], $request->input('limit', Setting::PAGE_RESULT_LIMIT))));
    }

    /**
     * List of disputed Orders
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function disputed(Request $request): JsonResponse
    {
        return $this->jsonSuccess(OrderResource::collection($this->service->indexByStatuses([OrderStatusEnum::DISPUTED->value, OrderStatusEnum::CANCEL_PENDING->value,  OrderStatusEnum::CANCELLED->value, ], $request->input('limit', Setting::PAGE_RESULT_LIMIT), true)));
    }

    /**
     * List of cancelled Orders
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function cancelled(Request $request): JsonResponse
    {
        return $this->jsonSuccess(OrderResource::collection($this->service->indexByStatuses([OrderStatusEnum::CANCELLED->value], $request->input('limit', Setting::PAGE_RESULT_LIMIT))));
    }

    /**
     * Show order
     *
     * @param Order $order
     *
     * @return JsonResponse
     */
    public function show(Order $order): JsonResponse
    {
         $order = $this->service->show($order);

        return $this->jsonSuccess(OrderResource::make($order));
    }

    /**
     * Adding delete order route
     *
     * @param Order $order
     *
     * @return JsonResponse
     */
    public function delete(Order $order): JsonResponse
    {
        $order->delete();

        return $this->jsonSuccess([], __('Order successfully deleted'));
    }

    /**
     * Approve cancellation request
     *
     * @param Order $order
     *
     * @return JsonResponse
     */
    public function approveCancellation(Order $order): JsonResponse
    {
        $this->service->approveCancellation($order);

        return $this->jsonSuccess([], __('Cancellation approved successfully'));
    }

    /**
     * Reject cancellation request
     *
     * @param Order $order
     *
     * @return JsonResponse
     */
    public function rejectCancellation(Order $order): JsonResponse
    {
        $this->service->rejectCancellation($order);

        return $this->jsonSuccess([], __('Cancellation rejected successfully'));
    }
}
