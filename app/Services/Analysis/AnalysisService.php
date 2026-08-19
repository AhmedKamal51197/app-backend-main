<?php

namespace App\Services\Analysis;

use App\Models\Commission;
use App\Models\Order;
use App\Models\Rate;
use App\Models\Refund;
use App\Models\User;
use App\Services\Order\AdminOrderService;
use App\Services\Project\AdminProjectService;
use App\Services\Service\AdminServiceService;
use Illuminate\Support\Facades\DB;

/**
 * A class defines the analysis service
 */
class AnalysisService
{
    public function __construct(
        protected AdminProjectService $adminProjectService,
        protected AdminServiceService $adminServiceService,
        protected AdminOrderService $adminOrderService
    ) {}

    /**
     * Get the analysis
     *
     * @return array
     */
    public function analysis(): array
    {
        return [
            'total_users' => $this->getTotalUsers(),
            'users_by_role' => $this->getUsersByRole(),
            'total_orders' => $this->getTotalOrders(),
            'orders_status' => $this->getOrdersByStatus(),
            'total_revenue' => $this->getTotalRevenue(),
            'average_order_value' => $this->getAverageOrderValue(),
            'customer_satisfaction' => $this->getAverageCustomerSatisfaction(),
            'revenue_last_year' => $this->getRevenueLastYear(),
            'user_growth' => $this->getUserGrowth(),
            'refunds' => $this->getMonthlyRefunds(),
            'projects_analytics' => $this->adminProjectService->getAnalytics('monthly'),
            'services_analytics' => $this->adminServiceService->getAnalytics('monthly'),
            'orders_analytics' => $this->adminOrderService->getAnalytics('monthly'),
        ];
    }

    /**
     * Get the total users
     *
     * @return mixed
     */
    private function getTotalUsers(): mixed
    {
        return User::count();
    }

    /**
     * Get the users by role
     *
     * @return array
     */
    public function getUsersByRole(): array
    {
        return User::with('roles')
            ->get()
            ->groupBy(fn ($user) => $user->roles->first()?->name ?? 'No Role')
            ->map->count()
            ->toArray();
    }

    /**
     * Get the total orders
     *
     * @return mixed
     */
    private function getTotalOrders(): mixed
    {
        return Order::count();
    }

    /**
     * Get orders by status
     *
     * @return mixed
     */
    private function getOrdersByStatus(): mixed
    {
        return Order::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');
    }

    /**
     * Get the total revenue
     *
     * @return mixed
     */
    private function getTotalRevenue(): mixed
    {
        $serviceRevenue = $this->adminServiceService->getRevenue();
        $projectRevenue = $this->adminProjectService->getRevenue();

        return $serviceRevenue + $projectRevenue;
    }

    /**
     * Get the average order value
     *
     * @return mixed
     */
    private function getAverageOrderValue(): mixed
    {
        return Order::avg('price');
    }

    /**
     * Get average customer satisfied
     *
     * @return mixed
     */
    private function getAverageCustomerSatisfaction(): mixed
    {
        return Rate::avg('rate');
    }

    /**
     * Get revenue last year
     *
     * @return mixed
     */
    private function getRevenueLastYear(): mixed
    {
        return Commission::selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->where('amount', '>', 0)
            ->whereYear('created_at', now()->subYear()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }

    /**
     * Get the user growth
     *
     * @return mixed
     */
    private function getUserGrowth(): mixed
    {
        return User::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }

    /**
     * Get monthly refunds
     *
     * @return mixed
     */
    private function getMonthlyRefunds(): mixed
    {
        return Refund::selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }
}
