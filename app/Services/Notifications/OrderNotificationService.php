<?php

namespace App\Services\Notifications;

use App\Models\Order;
use App\Notifications\Order\NewOrderNotification;
use App\Notifications\Order\OrderApprovedNotification;
use App\Notifications\Order\OrderCancellationRequestedNotification;
use App\Notifications\Order\OrderCancelledNotification;
use App\Notifications\Order\OrderCompletedNotification;
use App\Notifications\Order\OrderPurchasedNotification;
use App\Notifications\Order\OrderRefundedNotification;
use App\Notifications\Order\OrderRejectedNotification;
use App\Notifications\Order\OrderReleasedNotification;
use App\Notifications\Order\OrderRenewedNotification;
use App\Notifications\Order\OrderRequestReleaseNotification;
use App\Notifications\Order\OrderRevisionRequestedNotification;

/**
 * A class defines the notification for orders
 */
class OrderNotificationService
{
    /**
     * Info for the order
     *
     * @param Order $order
     *
     * @return array
     */
    public function info(Order $order): array
    {
        return [
            'code' => $order->code,
            'name' => $order->orderable->service->title ??  $order->orderable->title,
        ];
    }

    /**
     * Send notification for new order
     *
     * @param Order $order
     *
     * @return void
     */
    public function newOrder(Order $order): void
    {
        $info = $this->info($order);

        $order->seeker->notify(new OrderPurchasedNotification($info['code'], $info['name']));

        $order->provider->notify(new NewOrderNotification($info['code'], $info['name']));
    }

    /**
     * Send notification for approved
     *
     * @param Order $order
     *
     * @return void
     */
    public function approved(Order $order): void
    {
        $info = $this->info($order);

        $order->seeker->notify(new OrderApprovedNotification($info['code'], $info['name']));

        $order->provider->notify(new OrderApprovedNotification($info['code'], $info['name']));
    }

    /**
     * Send notification for reject order
     *
     * @param Order $order
     *
     * @return void
     */
    public function reject(Order $order): void
    {
        $info = $this->info($order);

        $order->seeker->notify(new OrderRejectedNotification($info['code'], $info['name']));

        $order->provider->notify(new OrderRejectedNotification($info['code'], $info['name']));
    }

    /**
     * Send notification for cancel order
     *
     * @param Order $order
     *
     * @return void
     */
    public function cancelled(Order $order): void
    {
        $info = $this->info($order);

        $order->seeker->notify(new OrderCancelledNotification($info['code'], $info['name']));

        $order->provider->notify(new OrderCancelledNotification($info['code'], $info['name']));
    }

    /**
     * Send notification for refund order
     *
     * @param Order $order
     *
     * @return void
     */
    public function refunded(Order $order): void
    {
        $info = $this->info($order);

        $order->seeker->notify(new OrderRefundedNotification($info['code'], $info['name']));

        $order->provider->notify(new OrderRefundedNotification($info['code'], $info['name']));
    }

    /**
     * Send notification for cancel requested order
     *
     * @param Order $order
     *
     * @return void
     */
    public function cancellationRequested(Order $order): void
    {
        $info = $this->info($order);

        $order->seeker->notify(new OrderCancellationRequestedNotification($info['code'], $info['name']));

        $order->provider->notify(new OrderCancellationRequestedNotification($info['code'], $info['name']));
    }

    /**
     * Send notification for complete order
     *
     * @param Order $order
     *
     * @return void
     */
    public function complete(Order $order): void
    {
        $info = $this->info($order);

        $order->seeker->notify(new OrderCompletedNotification($info['code'], $info['name']));

        $order->provider->notify(new OrderCompletedNotification($info['code'], $info['name']));
    }

    /**
     * Send notification for release order
     *
     * @param Order $order
     *
     * @return void
     */
    public function release(Order $order): void
    {
        $info = $this->info($order);

        $order->seeker->notify(new OrderReleasedNotification($info['code'], $info['name']));

        $order->provider->notify(new OrderReleasedNotification($info['code'], $info['name']));
    }

    /**
     * Send notification for release request for order
     *
     * @param Order $order
     *
     * @return void
     */
    public function releaseRequested(Order $order): void
    {
        $info = $this->info($order);

        $order->seeker->notify(new OrderRequestReleaseNotification($info['code'], $info['name']));

        $order->provider->notify(new OrderRequestReleaseNotification($info['code'], $info['name']));
    }

    /**
     * Send notification for renew order
     *
     * @param Order $order
     *
     * @return void
     */
    public function renew(Order $order): void
    {
        $info = $this->info($order);

        $order->seeker->notify(new OrderRenewedNotification($info['code'], $info['name']));

        $order->provider->notify(new OrderRenewedNotification($info['code'], $info['name']));
    }

    /**
     * Send notification for revision requested order
     *
     * @param Order $order
     *
     * @return void
     */
    public function revisionRequested(Order $order): void
    {
        $info = $this->info($order);

        $order->seeker->notify(new OrderRevisionRequestedNotification($info['code'], $info['name']));

        $order->provider->notify(new OrderRevisionRequestedNotification($info['code'], $info['name']));
    }
}
