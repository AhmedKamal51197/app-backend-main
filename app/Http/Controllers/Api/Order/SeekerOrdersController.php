<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\Api\Order\OneTimeOrderResource;
use App\Http\Resources\Api\Order\OrderResource;
use App\Http\Resources\Api\Order\PartTimeOrderResource;
use App\Http\Resources\Api\Order\SeekerOrdersResource;
use App\Models\Order;
use App\Models\Setting;
use App\Services\Order\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * A class defines the seeker order controller
 */
class SeekerOrdersController extends BaseApiController
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
        $orders = $this->service->seekerOrders(request()->user(), $request->input('limit', Setting::PAGE_RESULT_LIMIT));

        return $this->jsonSuccess(SeekerOrdersResource::make($orders));
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
        $order->load(['orderable', 'orderable.service', 'seeker', 'provider']);

        return $this->jsonSuccess(OrderResource::make($order));
    }

    /**
     * One Time Services
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function one_time_services(Request $request): JsonResponse
    {
        $orders = $this->service->one_time(request()->user(), $request->input('limit', Setting::PAGE_RESULT_LIMIT));

        return $this->jsonSuccess(OneTimeOrderResource::make($orders));
    }

    /**
     * One Time Services
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function part_time_services(Request $request): JsonResponse
    {
        $orders = $this->service->part_time(request()->user(), $request->input('limit', Setting::PAGE_RESULT_LIMIT));

        return $this->jsonSuccess(PartTimeOrderResource::make($orders));
    }
}
