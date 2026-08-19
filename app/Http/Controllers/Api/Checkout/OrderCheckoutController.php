<?php

namespace App\Http\Controllers\Api\Checkout;

use App\Enums\OrderStatusEnum;
use App\Enums\ServiceTypeEnum;
use App\Events\LogExceptionEvent;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\Checkout\OrderCheckoutRequest;
use App\Models\Order;
use App\Services\Order\RenewOrderService;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * A class defines the Renew order checkout controller
 */
class OrderCheckoutController extends BaseApiController
{
    /**
     * Call the service
     *
     * @param RenewOrderService $service
     */
    public function __construct(protected RenewOrderService $service)
    {

    }

    /**
     * Store a new checkout
     *
     * @param OrderCheckoutRequest $request
     *
     * @return JsonResponse
     */
    public function checkout(OrderCheckoutRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $order = Order::where('uuid', $data['order_id'])->firstOrFail();


            if ($order->orderable->service->type != ServiceTypeEnum::PART_TIME->value) {
                return $this->jsonError(__('This order is not part time'));
            }

            if (!($order->main_order)) {
                return $this->jsonError(__('This order is not original order you cant renew'));
            }

            if (
                $order->status !== OrderStatusEnum::RELEASED->value &&
                $order->status !== OrderStatusEnum::COMPLETED->value) {

                return $this->jsonError(__('Please complete the previous order to renew new one'));
            }
            if ($order->renewals()->exists()) {

                $lastRenewal = $order->renewals()
                    ->orderByDesc('created_at')
                    ->first();

                if (
                    $lastRenewal->status !== OrderStatusEnum::RELEASED->value &&
                    $lastRenewal->status !== OrderStatusEnum::COMPLETED->value) {

                    return $this->jsonError(__('Please complete the previous order to renew new one'));
                }
            }

            $checkout = $this->service->checkout($order, request()->user(), $request->validated());

            return $this->jsonSuccess(($checkout), __('Checkout Added successfully'));

        } catch (Exception $exception) {
            event(new LogExceptionEvent($exception));

            return $this->jsonError(__('Error while creating checkout, please try again later'));
        }
    }
}


