<?php

namespace App\Services\Dashboard;

use App\Enums\OrderStatusEnum;
use App\Http\Resources\Api\Order\OrderResource;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Carbon;

/**
 * A class defines the Dashboard service
 */
class DashboardService
{
    /**
     * Get dashboard analytics data
     *
     * @param User $user
     * @return array
     */
    public function getDashboardAnalytics(User $user): array
    {
        return [
            'chartData' => $this->getMonthlyRevenueChart($user),
            'currentMonthRevenue' => $this->getCurrentMonthRevenue($user),
            'ordersInQueue' => $this->getOrdersInQueue($user),
            'currentOrders' => $this->getCurrentOrders($user),
        ];
    }

    /**
     * Get monthly revenue chart data for the last 12 months
     *
     * @param User $user
     * @return array
     */
    private function getMonthlyRevenueChart(User $user): array
    {
        $chartData = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            $revenue = $user->wallets()
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('credit');

            $chartData[] = [
                'name' => $date->format('M'),
                'value' => (float)$revenue
            ];
        }

        return $chartData;
    }

    /**
     * Get current month revenue
     *
     * @param User $user
     * @return float
     */
    private function getCurrentMonthRevenue(User $user): float
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        return (float)$user->wallets()
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('credit');
    }

    /**
     * Get orders in queue (pending approval)
     *
     * @param User $user
     * @return int
     */
    private function getOrdersInQueue(User $user): int
    {
        return Order::where('provider_id', $user->id)
            ->where('status', OrderStatusEnum::APPROVAL_PENDING->value)
            ->count();
    }

    /**
     * Get current active orders with buyer details
     *
     * @param User $user
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    private function getCurrentOrders(User $user): AnonymousResourceCollection
    {
        $orders = Order::with(['seeker', 'orderable'])
            ->where('provider_id', $user->id)
            ->whereIn('status', [
                OrderStatusEnum::IN_PROGRESS->value,
                OrderStatusEnum::RELEASE_PENDING->value
            ])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return OrderResource::collection($orders);
    }

    /**
     * Calculate delivery time for an order
     *
     * @param Order $order
     * @return string
     */
    private function calculateDeliveryTime(Order $order): string
    {
        // You can customize this logic based on your business rules
        if ($order->orderable && method_exists($order->orderable, 'delivery_days')) {
            return $order->orderable->delivery_days . ' days';
        }

        return $order->time;
    }

    /**
     * Map order status to frontend format
     *
     * @param string $status
     *
     * @return string
     */
    private function mapOrderStatus(string $status): string
    {
        return match ($status) {
            OrderStatusEnum::IN_PROGRESS->value => 'in_progress',
            OrderStatusEnum::RELEASE_PENDING->value => 'revision',
            OrderStatusEnum::COMPLETED->value => 'completed',
            OrderStatusEnum::DISPUTED->value => 'disputed',
            default => 'pending'
        };
    }

    /**
     * Check if order is paid
     *
     * @param Order $order
     *
     * @return bool
     */
    private function isOrderPaid(Order $order): bool
    {
        return in_array($order->status, [
            OrderStatusEnum::IN_PROGRESS->value,
            OrderStatusEnum::REVISION->value,
            OrderStatusEnum::COMPLETED->value,
            OrderStatusEnum::RELEASE_PENDING->value,
            OrderStatusEnum::RELEASED->value
        ]);
    }
}
