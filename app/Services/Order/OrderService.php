<?php

namespace App\Services\Order;

use App\Actions\Attachments\StoreAttachmentAction;
use App\Actions\Files\GuessFileTypeAction;
use App\Actions\Notification\NotificationAction;
use App\Enums\AttachmentStorageEnum;
use App\Enums\NotificationReferenceEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\ProjectStatusEnum;
use App\Enums\ProposalStatusEnum;
use App\Enums\RefundRequestStatusEnum;
use App\Enums\ServiceTypeEnum;
use App\Events\LogExceptionEvent;
use App\Models\Chat;
use App\Models\Commission;
use App\Models\CommissionSetting;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\PaymentGateway\PaymentGatewayCheckout;
use App\Models\Project;
use App\Models\Rate;
use App\Models\RefundRequest;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\Receipts\PaymentReceiptNotification;
use App\Notifications\Wallet\WalletCreditNotification;
use App\Services\Notifications\OrderNotificationService;
use App\Services\Receipt\ReceiptService;
use App\Services\Wallet\WalletService;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\Project\ProjectService;

/**
 * A class defines the order service
 */
class OrderService
{
    /**
     * Load the project service
     */
    public function __construct(protected ProjectService $projectService)
    {
    }
    /**
     * Index the orders
     *
     * @param User $user
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function index(User $user, int $perPage = Setting::PAGE_RESULT_LIMIT): LengthAwarePaginator
    {
        return Order::query()
            ->with(['category', 'subCategory'])
            ->orderByDesc('created_at')
            ->where('provider_id', '=', $user->getAttribute('id'))
            ->where('main_order', '=', 1)
            ->orWhere('seeker_id', '=', $user->getAttribute('id'))
            ->paginate($perPage);
    }

    /**
     * Index the orders
     *
     * @param User $user
     * @param int $perPage
     *
     * @return array
     */
    public function seekerOrders(User $user, int $perPage = Setting::PAGE_RESULT_LIMIT): array
    {
        $partTime = Order::query()
            ->where(function ($query) use ($user) {
                $query->where('provider_id', $user->id)
                    ->orWhere('seeker_id', $user->id);
            })
            ->whereHasMorph(
                'orderable',
                [ServicePackage::class],
                function ($query) {
                    $query->whereHas('service', function ($q) {
                        $q->where('type', ServiceTypeEnum::PART_TIME->value);
                    });
                }
            )
            ->with(['orderable', 'orderable.service', 'orderable.service.attachments', 'provider', 'category', 'subCategory'])
            ->orderByDesc('created_at')
            ->paginate($perPage);

        $oneTime = Order::query()
            ->where(function ($query) use ($user) {
                $query->where('provider_id', $user->getAttribute('id'))
                    ->orWhere('seeker_id', $user->getAttribute('id'));
            })
            ->whereHasMorph(
                'orderable',
                [ServicePackage::class],
                function ($query) {
                    $query->whereHas('service', function ($q) {
                        $q->where('type', ServiceTypeEnum::ONE_TIME->value);
                    });
                }
            )
            ->with(['orderable', 'orderable.service', 'orderable.service.attachments', 'provider', 'category', 'subCategory'])
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return [
            'part_time' => $partTime,
            'one_time' => $oneTime
        ];
    }

    /**
     * Store prooject as order
     *
     * @param PaymentGatewayCheckout $checkout
     * @param Project $project
     * @param User $seeker
     *
     * @return Order
     *
     * @throws Exception
     */
    public function storeProject(PaymentGatewayCheckout $checkout, Project $project, User $seeker): Order
    {
        DB::beginTransaction();
        try {
            NotificationAction::send($checkout->user, NotificationReferenceEnum::SYSTEM, '', __('New payment transaction'), __('You have a new credit to your wallet'), false);

            $walletCredit = WalletService::createWallet($checkout->user, 0, $checkout->amount, __('Payment Transaction To Website'), $checkout);
            ReceiptService::createReceipt($checkout->user, $checkout->amount, $walletCredit);

            $commissionSetting = CommissionSetting::where([
                'key' => 'seeker',
            ])->first();
            $proposal = $project->selectedProposal;

            if (!$proposal) {
                throw new Exception(__('No selected proposal found for this project'));
            }
            $order = Order::create([
                'price' => $proposal->price,
                'seeker_commissions' => ($commissionSetting->value * $proposal->price),
                'code' => '123456',
                'orderable_id' => $project->getAttribute('id'),
                'orderable_type' => Project::class,
                'status' => OrderStatusEnum::APPROVAL_PENDING->value,
                'seeker_id' => $seeker->getAttribute('id'),
                'provider_id' => $proposal->user_id,
                'category_id' =>  $project->category_id,
                'sub_category_id' =>  $project->sub_category_id,
                'time' => $proposal->time,
            ]);

            // Link existing chat to this order if exists
            $existingChat = Chat::where('chattable_type', Project::class)
                ->where('chattable_id', $project->id)
                ->whereHas('participants', function ($q) use ($seeker) {
                    $q->where('user_id', $seeker->id);
                })
                ->whereHas('participants', function ($q) use ($proposal) {
                    $q->where('user_id', $proposal->user_id);
                })
                ->whereNull('order_id')
                ->first();

            if ($existingChat) {
                $existingChat->update(['order_id' => $order->id]);
            }

            Commission::create([
                'title' => 'Order seeker commissions',
                'amount' => ($commissionSetting->value * $proposal->price),
                'payable_id' => $order->getAttribute('id'),
                'payable_type' => Order::class,
            ]);

            WalletService::createWallet($checkout->user, $checkout->amount, 0, __('Payment Order'), $checkout);

            NotificationAction::send($checkout->user, NotificationReferenceEnum::SYSTEM, '', __('New transaction'), __('You have paid and order'), false);

            NotificationAction::send($order->seeker, NotificationReferenceEnum::ORDER, $order->getAttribute('uuid'), __('New order paid'), __('Your order has been placed'), true);

            NotificationAction::send($order->provider, NotificationReferenceEnum::ORDER, $order->getAttribute('uuid'), __('New order sent to you'), __('You have new order, Check to accept'), true);

            DB::commit();

            (new OrderNotificationService())->newOrder($order);

            $checkout->user->notify(new PaymentReceiptNotification(
                name: $checkout->user->name,
                amount: $checkout->amount,
                date: now()->format('Y-m-d H:i:s')
            ));

            return $order;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Store new order
     *
     * @param PaymentGatewayCheckout $checkout
     * @param ServicePackage $servicePackage
     * @param User $seeker
     *
     * @return Order
     *
     * @throws Exception
     */
    public function store(PaymentGatewayCheckout $checkout, ServicePackage $servicePackage, User $seeker): Order
    {
        DB::beginTransaction();
        try {
            NotificationAction::send($checkout->user, NotificationReferenceEnum::SYSTEM, '', __('New payment transaction'), __('You have a new credit to your wallet'), false);

            $walletCredit = WalletService::createWallet($checkout->user, 0, $checkout->amount, __('Payment Transaction To Website'), $checkout);
            ReceiptService::createReceipt($checkout->user, $checkout->amount, $walletCredit);

            $commissionSetting = CommissionSetting::where([
                'key' => 'seeker',
            ])->first();

            $seekerId = $seeker->getAttribute('id');
            $providerId = $servicePackage->service->user_id;

            $order = Order::create([
                'price' => $servicePackage->price,
                'seeker_commissions' => ($commissionSetting->value * $servicePackage->price),
                'code' => '123456',
                'orderable_id' => $servicePackage->getAttribute('id'),
                'orderable_type' => ServicePackage::class,
                'status' => OrderStatusEnum::APPROVAL_PENDING->value,
                'seeker_id' => $seekerId,
                'allowed_revisions' => $servicePackage->getAttribute('revisions'),
                'provider_id' => $providerId,
                'category_id' =>  $servicePackage->service->category_id,
                'sub_category_id' =>  $servicePackage->service->sub_category_id,
                'unlimited_revisions' => $servicePackage->getAttribute('unlimited_revisions')
            ]);

            // Link existing chat to this order if exists
            $existingChat = Chat::where('chattable_type', Service::class)
                ->where('chattable_id', $servicePackage->service->id)
                ->whereHas('participants', function ($q) use ($seekerId) {
                    $q->where('user_id', $seekerId);
                })
                ->whereHas('participants', function ($q) use ($providerId) {
                    $q->where('user_id', $providerId);
                })
                ->whereNull('order_id')
                ->first();

            if ($existingChat) {
                $existingChat->update(['order_id' => $order->id]);
            }

            Commission::create([
                'title' => 'Order seeker commissions',
                'amount' => ($commissionSetting->value * $servicePackage->price),
                'payable_id' => $order->getAttribute('id'),
                'payable_type' => Order::class,
            ]);

            WalletService::createWallet($checkout->user, $checkout->amount, 0, __('Payment Order'), $checkout);

            NotificationAction::send($checkout->user, NotificationReferenceEnum::SYSTEM, '', __('New transaction'), __('You have paid and order'), false);

            NotificationAction::send($order->seeker, NotificationReferenceEnum::ORDER, $order->getAttribute('uuid'), __('New order paid'), __('Your order has been placed'), true);

            NotificationAction::send($order->provider, NotificationReferenceEnum::ORDER, $order->getAttribute('uuid'), __('New order sent to you'), __('You have new order, Check to accept'), true);

            DB::commit();

            (new OrderNotificationService())->newOrder($order);

            $checkout->user->notify(new PaymentReceiptNotification(
                name: $checkout->user->name,
                amount: $checkout->amount,
                date: now()->format('Y-m-d H:i:s')
            ));

            return $order;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * One time services
     *
     * @param User $user
     * @param int $perPage
     *
     * @return array
     */
    public function one_time(User $user, int $perPage = Setting::PAGE_RESULT_LIMIT): array
    {
        $statuses = [
            'approval_pending' => [OrderStatusEnum::APPROVAL_PENDING->value],
            'in_progress' => [
                OrderStatusEnum::IN_PROGRESS->value,
                OrderStatusEnum::RELEASE_PENDING->value,
                OrderStatusEnum::CANCEL_PENDING->value,
            ],
            'rejected' => [OrderStatusEnum::REJECTED->value],
            'completed' => [
                OrderStatusEnum::COMPLETED->value,
                OrderStatusEnum::RELEASED->value,
            ],
            'disputed' => [OrderStatusEnum::DISPUTED->value],
            'refunded' => [OrderStatusEnum::REFUNDED->value],
            'cancelled' => [OrderStatusEnum::CANCELLED->value],
            'revision' => [OrderStatusEnum::REVISION->value],
        ];

        return array_map(function ($statusList) use ($perPage, $user) {
            return $this->getOrdersByStatus($user, ServiceTypeEnum::ONE_TIME->value, $statusList, $perPage);
        }, $statuses);
    }

    /**
     * One time services
     *
     * @param User $user
     * @param int $perPage
     *
     * @return array
     */
    public function part_time(User $user, int $perPage = Setting::PAGE_RESULT_LIMIT): array
    {
        $statuses = [
            'in_progress' => [
                OrderStatusEnum::IN_PROGRESS->value,
                OrderStatusEnum::RELEASE_PENDING->value,
                OrderStatusEnum::CANCEL_PENDING->value,
                OrderStatusEnum::REVISION->value,
                OrderStatusEnum::DISPUTED->value,
                OrderStatusEnum::APPROVAL_PENDING->value,
            ],
            'previous' => [
                OrderStatusEnum::COMPLETED->value,
                OrderStatusEnum::RELEASED->value,
                OrderStatusEnum::CANCELLED->value,
                OrderStatusEnum::REJECTED->value,
                OrderStatusEnum::REFUNDED->value,
            ],
        ];

        return array_map(function ($statusList) use ($perPage, $user) {
            return $this->getOrdersByStatus($user, ServiceTypeEnum::PART_TIME->value, $statusList, $perPage);
        }, $statuses);
    }

    /**
     * Get order by status
     *
     * @param User $user
     * @param string $type
     * @param array $statuses
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    private function getOrdersByStatus(User $user, string $type, array $statuses, int $perPage): LengthAwarePaginator
    {
        return Order::query()
            ->whereIn('status', $statuses)
            ->whereHas('orderable.service', function ($query) use ($type) {
                $query->where('type', $type);
            })->where(function ($query) use ($user) {
                $query->where('provider_id', $user->getAttribute('id'))
                    ->orWhere('seeker_id', $user->getAttribute('id'));
            })
            ->with([
                'provider',
                'orderable',
                'orderable.service',
                'orderable.service.attachments',
                'category',
                'subCategory'
            ])
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Approve the order
     *
     * @param Order $order
     *
     * @return void
     *
     * @throws Exception
     */
    public function approve(Order $order): void
    {
        DB::beginTransaction();
        try {
            NotificationAction::send($order->seeker, NotificationReferenceEnum::ORDER, $order->getAttribute('uuid'), __('Order approved'), __('The order has been approved'), true);
            NotificationAction::send($order->provider, NotificationReferenceEnum::ORDER, $order->getAttribute('uuid'), __('Order approved'), __('The order has been approved'), true);

            $order->update([
                'status' => OrderStatusEnum::IN_PROGRESS->value,
                'approved_at' => now()
            ]);

            if($order->orderable instanceof Project){
                $this->projectService->updateStatusWhenApproved($order->orderable);
            }

            (new OrderNotificationService())->approved($order);


            DB::commit();

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Reject order
     *
     * @param Order $order
     *
     * @return void
     *
     * @throws Exception
     */
    public function reject(Order $order): void
    {
        DB::beginTransaction();
        try {
            NotificationAction::send($order->seeker, NotificationReferenceEnum::ORDER, $order->getAttribute('uuid'), __('Order rejected'), __('The order has been rejected'), true);
            NotificationAction::send($order->provider, NotificationReferenceEnum::ORDER, $order->getAttribute('uuid'), __('Order rejected'), __('The order has been rejected'), true);

            $order->update(['status' => OrderStatusEnum::REJECTED->value]);


           if ($order->orderable instanceof Project){
               $this->projectService->updateStatusWhenRejected($order->orderable);
           }

            RefundRequest::create([
                'amount' => $order->getAttribute('price'),
                'refundable_id' => $order->getAttribute('id'),
                'refundable_type' => Order::class,
                'status' => RefundRequestStatusEnum::PENDING->value,
                'reason' => __('Order rejected'),
            ]);

            NotificationAction::send($order->seeker, NotificationReferenceEnum::SYSTEM, '', __('Refund requested'), __('You requested new refund'), true);

            (new OrderNotificationService())->reject($order);

            DB::commit();

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Check if the user is the order seeker
     *
     * @param User $user
     * @param Order $order
     *
     * @return bool
     */
    public function isSeeker(User $user, Order $order): bool
    {
        if ($order->getAttribute('seeker_id') === $user->getAttribute('id')) {
            return true;
        }
        return false;
    }

    /**
     * Check if the order user is the provider
     *
     * @param User $user
     * @param Order $order
     *
     * @return bool
     */
    public function isProvider(User $user, Order $order): bool
    {
        if ($order->getAttribute('provider_id') === $user->getAttribute('id')) {
            return true;
        }

        return false;
    }

    /**
     * Request cancel order
     *
     * @param Order $order
     * @param array $data
     *
     * @return void
     *
     * @throws Exception
     */
    public function requestCancel(Order $order, array $data = []): void
    {
        DB::beginTransaction();
        try {
            $user = request()->user();
            $cancelledBy = null;
            if ($this->isSeeker($user, $order)) {
                $cancelledBy = 'seeker';
            } elseif ($this->isProvider($user, $order)) {
                $cancelledBy = 'provider';
            }

            NotificationAction::send($order->seeker, NotificationReferenceEnum::ORDER, $order->getAttribute('uuid'), __('Order cancellation requested'), __('The order has been requested to cancel'), true);
            NotificationAction::send($order->provider, NotificationReferenceEnum::ORDER, $order->getAttribute('uuid'), __('Order cancellation requested'), __('The order has been requested to cancel'), true);

            $order->update([
                'status' => OrderStatusEnum::CANCEL_PENDING->value,
                'cancellation_reason' => $data['cancellation_reason'] ?? null,
                'cancelled_by' => $cancelledBy,
                'disputed_by' => $cancelledBy
            ]);

            RefundRequest::create([
                'amount' => $order->getAttribute('price'),
                'refundable_id' => $order->getAttribute('id'),
                'refundable_type' => Order::class,
                'status' => RefundRequestStatusEnum::PENDING->value,
                'reason' => __('Order cancelled'),
            ]);

            if($order->orderable instanceof Project){
                $this->projectService->cancel($order->orderable, ['cancellation_reason' => $data['cancellation_reason'] ?? 'Order request Cancel'], false);
            }

            NotificationAction::send($order->seeker, NotificationReferenceEnum::SYSTEM, '', __('Refund requested'), __('You requested new refund'), true);

            (new OrderNotificationService())->cancellationRequested($order);

            DB::commit();

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Request dispute order
     *
     * @param Order $order
     * @param array $data
     *
     * @return void
     *
     * @throws Exception
     */
    public function requestDispute(Order $order, array $data = []): void
    {
        DB::beginTransaction();
        try {
            $user = request()->user();
            $disputedBy = null;
            if ($this->isSeeker($user, $order)) {
                $disputedBy = 'seeker';
            } elseif ($this->isProvider($user, $order)) {
                $disputedBy = 'provider';
            }

            NotificationAction::send($order->seeker, NotificationReferenceEnum::ORDER, $order->getAttribute('uuid'), __('Order disputed'), __('The order has been disputed'), true);
            NotificationAction::send($order->provider, NotificationReferenceEnum::ORDER, $order->getAttribute('uuid'), __('Order disputed'), __('The order has been disputed'), true);

            $order->update([
                'status' => OrderStatusEnum::DISPUTED->value,
                'disputed_reason' => $data['disputed_reason'] ?? null,
                'disputed_by' => $disputedBy,
            ]);

            if ($order->orderable instanceof Project) {
                $order->orderable->update([
                    'status' => ProjectStatusEnum::DISPUTED->value
                ]);

                if ($order->orderable->selectedProposal) {
                    $order->orderable->selectedProposal->update([
                        'status' => ProposalStatusEnum::DISPUTED->value
                    ]);
                }
            }

            DB::commit();

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Request cancel order
     *
     * @param Order $order
     * @param array $data
     *
     * @return void
     *
     * @throws Exception
     */
    public function requestRevision(Order $order, array $data): void
    {
        DB::beginTransaction();
        try {
            NotificationAction::send($order->seeker, NotificationReferenceEnum::ORDER, $order->getAttribute('uuid'), __('Order revision requested'), __('The order has been requested to revision'), false);
            NotificationAction::send($order->provider, NotificationReferenceEnum::ORDER, $order->getAttribute('uuid'), __('Order revision requested'), __('The order has been requested to revision'), true);

            $order->update(['status' => OrderStatusEnum::REVISION->value]);
            $order->increment('revisions');

            OrderHistory::create([
                'order_id' => $order->getAttribute('id'),
                'initiator_id' => $order->seeker->id,
                'details' => $data['details'],
                'status' => OrderStatusEnum::REVISION->value,
            ]);

            (new OrderNotificationService())->revisionRequested($order);

            DB::commit();

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Request cancel order
     *
     * @param Order $order
     * @param array $data
     *
     * @return void
     *
     * @throws Exception
     */
    public function requestRelease(Order $order, array $data): void
    {
        DB::beginTransaction();
        try {
            $orderHistory = OrderHistory::create([
                'order_id' => $order->getAttribute('id'),
                'initiator_id' => $order->seeker->id,
                'details' => $data['details'],
                'status' => OrderStatusEnum::RELEASE_PENDING->value,
            ]);

            if (isset($data['attachments'])) {
                StoreAttachmentAction::store($orderHistory, $data['attachments'], 'attachments', false);
            }

            NotificationAction::send($order->seeker, NotificationReferenceEnum::ORDER, $order->getAttribute('uuid'), __('Order release requested'), __('The order has been requested to release'), false);
            NotificationAction::send($order->provider, NotificationReferenceEnum::ORDER, $order->getAttribute('uuid'), __('Order release requested'), __('The order has been requested to release'), true);

            $order->update(['status' => OrderStatusEnum::RELEASE_PENDING->value]);

            (new OrderNotificationService())->releaseRequested($order);

            DB::commit();

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Request cancel order
     *
     * @param Order $order
     * @param array $data
     *
     * @return void
     *
     * @throws Exception
     */
    public function rate(Order $order, array $data): void
    {
        DB::beginTransaction();
        try {
            $user = request()->user();

            // Check if user already rated this order
            $existingRate = Rate::where('order_id', $order->getAttribute('id'))
                ->where('user_id', $user->id)
                ->first();

            if ($existingRate) {
                DB::rollBack();
                throw new Exception(__('You have already rated this order'));
            }

            // Determine who is being rated
            $ratedUserId = $this->isSeeker($user, $order)
                ? $order->provider_id
                : $order->seeker_id;

            OrderHistory::create([
                'order_id' => $order->getAttribute('id'),
                'initiator_id' => $user->id,
                'details' => 'rated',
                'status' => $order->getAttribute('status'),
            ]);

            Rate::create([
                'user_id' => $user->id,
                'rated_user_id' => $ratedUserId,
                'order_id' => $order->getAttribute('id'),
                'comment' => $data['comment'],
                'rate' => $data['rate'],
            ]);

            // Determine who to notify
            if ($this->isSeeker($user, $order)) {
                NotificationAction::send($order->provider, NotificationReferenceEnum::ORDER, $order->getAttribute('uuid'), __('Order rated'), __('The seeker has rated the order'), true);
            } else {
                NotificationAction::send($order->seeker, NotificationReferenceEnum::ORDER, $order->getAttribute('uuid'), __('Order rated'), __('The provider has rated the order'), true);
            }

            // Check if both parties have rated
            $totalRatings = Rate::where('order_id', $order->getAttribute('id'))->count();

            // If this is the first rating and status is RELEASED, change to COMPLETED
            if ($totalRatings === 1 && $order->getAttribute('status') === OrderStatusEnum::RELEASED->value) {
                $order->update(['status' => OrderStatusEnum::COMPLETED->value]);

                if ($order->orderable instanceof Project) {
                    $this->projectService->complete($order->orderable);
                }

                (new OrderNotificationService())->complete($order);
            }

            DB::commit();

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Release the order
     *
     * @param Order $order
     *
     * @return void
     *
     * @throws Exception
     */
    public function release(Order $order): void
    {
        DB::beginTransaction();
        try {
            NotificationAction::send($order->seeker, NotificationReferenceEnum::ORDER, $order->getAttribute('uuid'), __('Order released successfully'), __('The order has been released'), true);
            NotificationAction::send($order->provider, NotificationReferenceEnum::ORDER, $order->getAttribute('uuid'), __('Order released successfully'), __('The order has been released'), true);

            $commissionSetting = CommissionSetting::where([
                'key' => 'provider',
            ])->first();

            $provider_commissions = ($order->getAttribute('price') * $commissionSetting->value);

            WalletService::createWallet($order->provider, 0, ($order->getAttribute('price') - $provider_commissions), __('Order released'), $order);

            Commission::create([
                'title' => 'Order provider commissions',
                'amount' => $provider_commissions,
                'payable_id' => $order->getAttribute('id'),
                'payable_type' => Order::class,
            ]);

            $order->update(['status' => OrderStatusEnum::RELEASED->value, 'provider_commissions' => $provider_commissions]);

            $order->provider->notify(new WalletCreditNotification(($order->getAttribute('price') - $provider_commissions)));

            NotificationAction::send($order->provider, NotificationReferenceEnum::SYSTEM, '', __('New order revenue'), __('Order released and price released'), true);

            OrderHistory::create([
                'order_id' => $order->getAttribute('id'),
                'initiator_id' => $order->seeker->id,
                'details' => 'released',
                'status' => OrderStatusEnum::RELEASED->value,
            ]);
            (new OrderNotificationService())->release($order);

            DB::commit();

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }
}
