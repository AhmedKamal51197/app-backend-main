<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\Order\StoreOrderMessageRequest;
use App\Http\Resources\Api\Order\OrderMessageResource;
use App\Models\Order;
use App\Services\Order\OrderMessageService;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * A class defines the order message controller
 */
class OrderMessagesController extends BaseApiController
{
    /**
     * Load the service
     *
     * @param OrderMessageService $service
     */
    public function __construct(protected OrderMessageService $service)
    {
    }

    /**
     * Show the order
     *
     * @param Order $order
     * @param StoreOrderMessageRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function store(Order $order, StoreOrderMessageRequest $request): JsonResponse
    {
        $message = $this->service->store(request()->user(), $order, $request->validated());

        return $this->jsonSuccess(OrderMessageResource::make($message), __('Message created successfully'));
    }
}
