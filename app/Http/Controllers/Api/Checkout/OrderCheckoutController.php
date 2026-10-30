<?php

namespace App\Http\Controllers\Api\Checkout;

use App\Enums\OrderStatusEnum;
use App\Enums\ServiceTypeEnum;
use App\Events\LogExceptionEvent;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\Checkout\OrderCheckoutRequest;
use App\Models\Order;
use App\Models\ServicePackage;
use App\Services\Checkout\CheckoutPricingService;
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

            if ($error = $this->renewError($order)) {
                return $this->jsonError($error);
            }

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

    /**
     * Invoice breakdown for renewing an order, shown before payment.
     *
     * @param OrderCheckoutRequest $request
     *
     * @return JsonResponse
     */
    public function preview(OrderCheckoutRequest $request): JsonResponse
    {
        $order = Order::where('uuid', $request->validated()['order_id'])->firstOrFail();

        if ($error = $this->renewError($order)) {
            return $this->jsonError($error);
        }

        return $this->jsonSuccess((new CheckoutPricingService())->breakdown((float) $order->orderable->price));
    }

    /**
     * Reason the current user can't renew this order, or null when allowed.
     *
     * @param Order $order
     *
     * @return string|null
     */
    private function renewError(Order $order): ?string
    {
        if ((int) $order->seeker_id !== (int) request()->user()->id) {
            return __('This action is unauthorized.');
        }

        if (!$order->orderable instanceof ServicePackage || !$order->orderable->service) {
            return __('This order is not part time');
        }

        return null;
    }
}


