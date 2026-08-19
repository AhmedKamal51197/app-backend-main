<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\Api\Order\OrderHistoryResource;
use App\Http\Resources\Api\Order\OrderResource;
use App\Models\Order;
use App\Models\Project;
use App\Models\Setting;
use App\Services\Order\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * A class defines the order controller
 */
class OrderController extends BaseApiController
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
     * List of the user orders
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $orders = $this->service->index(request()->user(), $request->input('limit', Setting::PAGE_RESULT_LIMIT));

        return $this->jsonSuccess(OrderResource::collection($orders));
    }

    /**
     * Show the order
     *
     * @param Order $order
     *
     * @return JsonResponse
     */
    public function show(Order $order): JsonResponse
    {
        if ($order->orderable instanceof Project){
            $order->load(['orderable', 'orderable.attachments', 'seeker', 'provider', 'messages', 'attachments', 'category', 'subCategory', 'rates', 'rates.user', 'rates.ratedUser']);
        }else{
            $order->load(['orderable', 'orderable.service', 'orderable.service.attachments', 'seeker', 'provider', 'messages', 'attachments', 'category', 'subCategory', 'rates', 'rates.user', 'rates.ratedUser']);
        }
        return $this->jsonSuccess(OrderResource::make($order));
    }

    /**
     * Show the order part-time
     *
     * @param Order $order
     *
     * @return JsonResponse
     */
    public function showPartTime(Order $order): JsonResponse
    {
        $order->load(['orderable', 'orderable.service', 'orderable.service.attachments', 'seeker', 'provider', 'messages', 'attachments', 'renewals', 'renewals.histories']);

        return $this->jsonSuccess(OrderResource::make($order));
    }

    /**
     * Define the histories
     *
     * @param Order $order
     *
     * @return JsonResponse
     */
    public function history(Order $order): JsonResponse
    {
        return $this->jsonSuccess(OrderHistoryResource::collection($order->histories));
    }
}
