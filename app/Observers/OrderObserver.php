<?php

namespace App\Observers;

use App\Enums\OrderStatusEnum;
use App\Models\Order;

/**
 * Order db observer
 */
class OrderObserver
{
    /**
     * Handle the Order "created" event.
     *
     * @param Order $order
     * @return void
     */
    public function created(Order $order): void
    {
        $order->histories()->create(
            [
                'initiator_id' => $order->getAttribute('seeker_id'),
                'status' => $order->status,
            ]
        );
    }

    /**
     * Handle the Order "updated" event.
     *
     * @param Order $order
     *
     * @return void
     */
    public function updated(Order $order): void
    {
//        $order->refresh();
//
//        $status = $order->status;
//
//        $details = match ($status) {
//            OrderStatusEnum::APPROVAL_PENDING->value => 'The seeker requested the provider to approve the orde',
//            OrderStatusEnum::IN_PROGRESS->value => 'provider accepted the order and start working',
//            OrderStatusEnum::REJECTED->value => 'provider rejected the order and the order will refunded',
//            OrderStatusEnum::COMPLETED->value => 'Order completed successfully',
//            OrderStatusEnum::RELEASE_PENDING->value => 'provider added new submit(attachments)',
//            OrderStatusEnum::RELEASED->value => 'seeker accepted the work and its done',
//            OrderStatusEnum::DISPUTED->value => 'Order has problems and it should resolved',
//            OrderStatusEnum::REFUNDED->value => 'Order price refunded to seeker',
//            OrderStatusEnum::CANCEL_PENDING->value => 'Order requested to cancel, admin should approve',
//            OrderStatusEnum::CANCELLED->value => 'Admin approved the cancel for the order',
//            default => 'Seeker requested revision the order',
//        };
//
//        // Create new history when update
//        $order->histories()->create(
//            [
//                'initiator_id' => request()->user()->id,
//                'status' => $status,
//                'details' => $details,
//            ]
//        );
    }

    /**
     * Handle the Order "deleted" event.
     *
     * @param Order $order
     *
     * @return void
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     *
     * @param Order $order
     *
     * @return void
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     *
     * @param Order $order
     *
     * @return void
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
