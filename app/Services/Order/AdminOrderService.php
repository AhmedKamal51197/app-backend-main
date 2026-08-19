<?php

namespace App\Services\Order;

use App\Actions\Categories\CategoriesAveragePricesAction;
use App\Actions\Notification\NotificationAction;
use App\Enums\NotificationReferenceEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\ProjectStatusEnum;
use App\Enums\ProposalStatusEnum;
use App\Events\LogExceptionEvent;
use App\Models\Commission;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\Project;
use App\Models\Refund;
use App\Models\ServicePackage;
use App\Models\Setting;
use App\Models\User;
use App\Services\Notifications\OrderNotificationService;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * A class defines the admin order service
 */
class AdminOrderService
{
    public function __construct(
        private CategoriesAveragePricesAction $categoriesAction
    ) {}
    /**
     * Index the orders
     *
     * @param string $search
     * @param int $limit
     * @param int $page
     * @param string $status
     * @param string $ordersCategoriesRate
     * @param string $orderBy
     *
     * @return array
     */
    public function index(string $search, int $limit, int $page, string $status, string $orderBy,  string $ordersCategoriesRate): array
    {
        $query = Order::query()->with(['category', 'orderable', 'seeker', 'provider']);

        // Search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('seeker', fn($q) => $q->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('provider', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        // Status filter
        if ($status) {
            $query->where('status', $status);
        }

        $query->orderBy('created_at', $orderBy === 'oldest' ? 'asc' : 'desc');
        $orders = $query->paginate($limit, ['*'], 'page', $page);

        return [
            'orders' => $orders,
            'analytics' => $this->getAnalytics($ordersCategoriesRate)
        ];
    }

    /**
     * Index the orders by statuses
     *
     * @param array $statuses
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function indexByStatuses(array $statuses, int $perPage = Setting::PAGE_RESULT_LIMIT, $loadData = false): LengthAwarePaginator
    {
        $query = Order::query()->whereIn('status', $statuses);

        if ($loadData) {
            $query->with(['seeker', 'provider', 'orderable']);
        }

        $orders = $query->orderByDesc('created_at')->paginate($perPage);

        if ($loadData) {
            $orders->getCollection()->each(function ($order) {
                if ($order->orderable instanceof ServicePackage) {
                    $order->orderable->load('service');
                }
            });
        }

        return $orders;
    }

    /**
     * Show order details
     *
     * @param Order $order
     *
     * @return Order
     */
    public function show(Order $order) : Order
    {
        $order->load([
            'seeker',
            'provider',
            'category',
            'subCategory',
            'attachments',
            'histories',
            'renewals',
            'histories.attachments',
            'orderable',
            'provider.bankAccount',
            'seeker.bankAccount',
            'seeker.paypal',
            'provider.paypal',
            'rates.user',
            'rates.ratedUser',
            'chat.messages.sender',
            'chat.messages.attachments',
        ]);

        if ($order->orderable instanceof ServicePackage) {
            $order->load(['orderable.service']);
        }

        return $order;
    }
    /**
     * Cancel order
     *
     * @param User $user
     * @param Order $order
     *
     * @return void
     *
     * @throws Exception
     */
    public function cancel(User $user, Order $order): void
    {
        DB::beginTransaction();
        try {
            OrderHistory::create([
                'order_id' => $order->getAttribute('id'),
                'initiator_id' => $user->getAttribute('id'),
                'details' => 'By Admin',
                'status' => OrderStatusEnum::CANCELLED->value,
            ]);

            NotificationAction::send($order->seeker, NotificationReferenceEnum::ORDER, $order->getAttribute('uuid'), __('Order cancelled'), __('The order has been cancelled'), true);
            NotificationAction::send($order->provider, NotificationReferenceEnum::ORDER, $order->getAttribute('uuid'), __('Order cancelled'), __('The order has been cancelled'), true);

            $order->update(['status' => OrderStatusEnum::CANCELLED->value]);

            (new OrderNotificationService())->cancelled($order);

            DB::commit();

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Refund order
     *
     * @param User $user
     * @param Order $order
     *
     * @return void
     *
     * @throws Exception
     */
    public function refund(User $user, Order $order): void
    {
        DB::beginTransaction();
        try {
            OrderHistory::create([
                'order_id' => $order->getAttribute('id'),
                'initiator_id' => $user->getAttribute('id'),
                'details' => 'By Admin',
                'status' => OrderStatusEnum::REFUNDED->value,
            ]);

            NotificationAction::send($order->seeker, NotificationReferenceEnum::ORDER, $order->getAttribute('uuid'), __('Order refunded'), __('The order has been refunded'), true);
            NotificationAction::send($order->provider, NotificationReferenceEnum::ORDER, $order->getAttribute('uuid'), __('Order refunded'), __('The order has been refunded'), true);

            Commission::create([
                'title' => 'Order provider commissions',
                'amount' => -($order->getAttribute('seeker_commissions')),
                'payable_id' => $order->getAttribute('id'),
                'payable_type' => Order::class,
            ]);

            Refund::create([
                'title' => 'Order refund',
                'amount' => $order->getAttribute('price'),
                'refundable_id' => $order->getAttribute('id'),
                'refundable_type' => Order::class,
            ]);

            $order->update(['status' => OrderStatusEnum::REFUNDED->value]);

            if ($order->orderable instanceof Project) {
                $order->orderable->update([
                    'status' => ProjectStatusEnum::REFUNDED->value
                ]);

                if ($order->orderable->selectedProposal) {
                    $order->orderable->selectedProposal->update([
                        'status' => ProposalStatusEnum::REFUNDED->value
                    ]);
                }
            }

            (new OrderNotificationService())->refunded($order);

            DB::commit();

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Get analytics data
     */
    public function getAnalytics($ordersCategoriesRate): array
    {
        $allTimeQuery = Order::query();

        $activeStatuses = [OrderStatusEnum::APPROVAL_PENDING->value, OrderStatusEnum::IN_PROGRESS->value, OrderStatusEnum::REVISION->value, OrderStatusEnum::RELEASE_PENDING->value];
        $openedStatuses = [OrderStatusEnum::APPROVAL_PENDING->value, OrderStatusEnum::IN_PROGRESS->value, OrderStatusEnum::REVISION->value];

        return [
            'active_orders' => (clone $allTimeQuery)->whereIn('status', $activeStatuses)->count(),
            'opened_orders' => (clone $allTimeQuery)->whereIn('status', $openedStatuses)->count(),
            'completed_orders' => (clone $allTimeQuery)->where('status', OrderStatusEnum::COMPLETED->value)->count(),
            'cancelled_orders' => (clone $allTimeQuery)->where('status', OrderStatusEnum::CANCELLED->value)->count(),
            'disputed_orders' => (clone $allTimeQuery)->where('status', OrderStatusEnum::DISPUTED->value)->count(),
            'completion_range' => $this->getCompletionRange($allTimeQuery),
            'submittion_time_average' => $this->getSubmissionTimeAverage($allTimeQuery),
            'orders_price_average' => round((clone $allTimeQuery)->avg('price') ?? 0, 2),
            'orders_prices_sum' => (clone $allTimeQuery)->sum('price') ?? 0,
            'orders_profit_sum' => Commission::whereHasMorph('payable', [Order::class], function ($q) use ($allTimeQuery) {
                $q->whereIn('id', (clone $allTimeQuery)->pluck('id'));
            })->sum('amount') ?? 0,
            'orders_categories' => $this->categoriesAction->calculate(new Order(), $ordersCategoriesRate)
        ];
    }

    /**
     * Get completion range percentage
     */
    private function getCompletionRange($dateQuery): int
    {
        $totalOrders = (clone $dateQuery)->count();
        $completedOrders = (clone $dateQuery)->where('status', OrderStatusEnum::COMPLETED->value)->count();

        return $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100) : 0;
    }

    /**
     * Get average submission time in days
     */
    private function getSubmissionTimeAverage($dateQuery): int
    {
        $completedOrders = (clone $dateQuery)
            ->where('status', OrderStatusEnum::COMPLETED->value)
            ->whereNotNull('approved_at')
            ->get(['approved_at', 'created_at']);

        if ($completedOrders->isEmpty()) {
            return 0;
        }

        $totalDays = $completedOrders->sum(function ($order) {
            return $order->approved_at->diffInDays($order->created_at);
        });

        return round($totalDays / $completedOrders->count());
    }

    /**
     * Admin approve cancellation request
     *
     * @param Order $order
     *
     * @return void
     *
     * @throws Exception
     */
    public function approveCancellation(Order $order): void
    {
        if ($order->status !== OrderStatusEnum::CANCEL_PENDING->value) {
            throw new Exception(__('Can only approve cancellation for pending cancellation orders'));
        }

        DB::beginTransaction();
        try {
            $order->update([
                'status' => OrderStatusEnum::CANCELLED->value,
                'cancelled_at' => now()
            ]);

            if ($order->orderable instanceof Project) {
                $order->orderable->update(['status' => ProjectStatusEnum::CANCELLED->value]);

                if ($order->orderable->selectedProposal) {
                    $order->orderable->selectedProposal->update(['status' => ProposalStatusEnum::CANCELLED]);
                }
            }

            NotificationAction::send($order->seeker, NotificationReferenceEnum::ORDER, $order->getAttribute('uuid'), __('Cancellation approved'), __('Your cancellation request has been approved'), true);
            NotificationAction::send($order->provider, NotificationReferenceEnum::ORDER, $order->getAttribute('uuid'), __('Cancellation approved'), __('The cancellation request has been approved'), true);

            DB::commit();

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Admin reject cancellation request
     *
     * @param Order $order
     *
     * @return void
     *
     * @throws Exception
     */
    public function rejectCancellation(Order $order): void
    {
        $allowedStatuses = [OrderStatusEnum::CANCEL_PENDING->value, OrderStatusEnum::DISPUTED->value];

        if (!in_array($order->status, $allowedStatuses)) {
            throw new Exception(__('Can only reject cancellation for pending cancellation or disputed orders'));
        }

        DB::beginTransaction();
        try {
            $order->update([
                'status' => OrderStatusEnum::IN_PROGRESS->value
            ]);

            if ($order->orderable instanceof Project) {

                $order->orderable->update([
                    'status' => ProjectStatusEnum::IN_PROGRESS->value,
                    'cancellation_reason' => null,
                    'cancelled_at' => null
                ]);

                if ($order->orderable->selectedProposal) {
                    $order->orderable->selectedProposal->update(['status' => ProposalStatusEnum::IN_PROGRESS]);
                }
            }

            NotificationAction::send($order->seeker, NotificationReferenceEnum::ORDER, $order->getAttribute('uuid'), __('Cancellation rejected'), __('Your cancellation request has been rejected'), true);
            NotificationAction::send($order->provider, NotificationReferenceEnum::ORDER, $order->getAttribute('uuid'), __('Cancellation rejected'), __('The cancellation request has been rejected'), true);

            DB::commit();

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }
}
