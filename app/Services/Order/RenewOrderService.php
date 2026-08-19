<?php

namespace App\Services\Order;

use App\Actions\Notification\NotificationAction;
use App\Enums\NotificationReferenceEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\PaymentGatewaysEnum;
use App\Events\LogExceptionEvent;
use App\Models\CommissionSetting;
use App\Models\Order;
use App\Models\PaymentGateway\PaymentGatewayCheckout;
use App\Models\ServicePackage;
use App\Models\User;
use App\Services\Notifications\OrderNotificationService;
use App\Services\Payment\MyFatoorahService;
use App\Services\Receipt\ReceiptService;
use App\Services\Wallet\WalletService;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * The service for renew the orders
 */
class RenewOrderService
{
    /**
     * Load the checkout service
     *
     * @param MyFatoorahService $myFatoorahService
     */
    public function __construct(protected MyFatoorahService $myFatoorahService)
    {
    }

    /**
     * Store new checkout for order
     *
     * @param Order $order
     * @param User $user
     *
     * @return array
     *
     * @throws Exception
     */
    public function checkout(Order $order, User $user, array $data): array
    {
        DB::beginTransaction();
        try {
            $package = $order->orderable;

            $commissionSetting = CommissionSetting::where([
                'key' => 'seeker_renew',
            ])->first();

            $checkout = PaymentGatewayCheckout::create([
                'amount' => $package->getAttribute('price') + ($package->getAttribute('price') * $commissionSetting->value),
                'payable_id' => $order->getAttribute('id'),
                'payable_type' => Order::class,
                'user_id' => $user->getAttribute('id'),
                "payment_gateway" => PaymentGatewaysEnum::MYFATOORAH->value,
            ]);

            $myFatoorahCheckout = $this->myFatoorahService->checkout(request()->user(), $checkout, $data['payment_method_id']);

            DB::commit();

            return $myFatoorahCheckout;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Function to renew the order
     *
     * @param PaymentGatewayCheckout $checkout
     * @param Order $order
     * @param User $seeker
     *
     * @return Order
     *
     * @throws Exception
     */
    public function store(PaymentGatewayCheckout $checkout, Order $order, User $seeker): Order
    {
        DB::beginTransaction();
        try {
            NotificationAction::send($checkout->user, NotificationReferenceEnum::SYSTEM, '', __('New payment transaction'), __('You have a new credit to your wallet'), false);

            $walletCredit = WalletService::createWallet($checkout->user, 0, $checkout->amount, __('Payment Transaction To Website Renew Order'), $checkout);
            ReceiptService::createReceipt($checkout->user, $checkout->amount, $walletCredit);

            $commissionSetting = CommissionSetting::where([
                'key' => 'seeker_renew',
            ])->first();

            $newOrder = Order::create([
                'price' => $order->orderable->price,
                'seeker_commissions' => ($commissionSetting->value * $order->orderable->price),
                'code' => '123456',
                'orderable_id' => $order->orderable->getAttribute('id'),
                'orderable_type' => ServicePackage::class,
                'status' => OrderStatusEnum::APPROVAL_PENDING->value,
                'seeker_id' => $seeker->getAttribute('id'),
                'allowed_revisions' => $order->orderable->getAttribute('revisions'),
                'provider_id' => $order->orderable->service->user_id,
                'parent_order_id' => $order->getAttribute('id'),
                'main_order' => false,
                'unlimited_revisions' => $order->orderable->unlimited_revisions
            ]);

            // Link the same chat from parent order to renewal order
            if ($order->chat) {
                $order->chat->update(['order_id' => $newOrder->id]);
            }

            WalletService::createWallet($checkout->user, $checkout->amount, 0, __('Renew Part time Order'), $checkout);

            NotificationAction::send($checkout->user, NotificationReferenceEnum::SYSTEM, '', __('New transaction'), __('You have renewed and order'), false);

            NotificationAction::send($order->seeker, NotificationReferenceEnum::ORDER, $order->getAttribute('uuid'), __('New order renewed'), __('Your order has been placed'), true);

            NotificationAction::send($order->provider, NotificationReferenceEnum::ORDER, $order->getAttribute('uuid'), __('New order renewed sent to you'), __('You have new order, Check to accept'), true);

            (new OrderNotificationService())->renew($order);

            DB::commit();

            return $newOrder;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }
}
