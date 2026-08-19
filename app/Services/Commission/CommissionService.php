<?php

namespace App\Services\Commission;

use App\Models\Commission;
use App\Models\Order;
use App\Models\Project;
use App\Models\Refund;
use App\Models\RefundRequest;
use App\Models\ServicePackage;

/**
 * A class defines the commission service
 */
class CommissionService
{
    /**
     * Index the commissions
     *
     * @param array $data
     *
     * @return array
     */
    public function index(array $data): array
    {
        $perPage = $data['limit'];

        $commissions = Commission::query()
            ->with([
            'payable.orderable',
            'payable.seeker',
            'payable.provider'
            ])
            ->orderByDesc('created_at')
            ->paginate($perPage);

        $commissions->getCollection()->each(function ($commission) {

            $orderable = $commission->payable->orderable;

            if ($orderable instanceof ServicePackage) {
                $commission->orderable_type = 'service';
                $orderable->load('service');

            } elseif ($orderable instanceof Project) {
                $commission->orderable_type = 'project';
            }
        });

        return [
            'commissions' => $commissions,
            'analytics' => $this->getRevenueAnalytics(),
            'chart' => $this->getRevenueChart($data['period'], $data['type']),
            'meta' => [
                'total_pages' => $commissions->lastPage(),
                'current_page' => $commissions->currentPage(),
                'total_items' => $commissions->total(),
                'per_page' => $commissions->perPage()
            ],
        ];
    }

    /**
     * Get revenue analytics
     *
     * @return array
     */
    public function getRevenueAnalytics(): array
    {
        $totalRevenue = Order::whereNotIn('status', [
            'cancelled',
            'cancel_pending',
            'rejected',
            'refunded'
        ])->sum('price');

        $pendingRevenue = Order::whereIn('status', [
            'approval_pending',
            'in_progress',
            'revision',
            'disputed'
        ])->sum('price');

        $completedRevenue = Order::whereIn('status', ['completed', 'release_pending', 'released'])
            ->sum('price');

        $refundedRevenue = Order::where('status', 'refunded')
            ->sum('price');

        $cancelledRevenue = Order::whereIn('status', ['cancelled', 'cancel_pending', 'rejected'])
            ->sum('price');

        // Fixed: Include all commissions (seeker + provider - refunds) for non-cancelled orders
        $netProfit = Commission::whereHasMorph('payable', [Order::class], function ($query) {
            $query->whereNotIn('status', [
                'cancelled',
                'cancel_pending',
                'rejected',
                'refunded'
            ]);
        })->sum('amount');

        $totalRefunds = Refund::sum('amount');

        $pendingRefunds = RefundRequest::where('status', 'pending')->sum('amount');


        return [
            'total_revenue' => round($totalRevenue, 2),
            'pending_revenue' => round($pendingRevenue, 2),
            'completed_revenue' => round($completedRevenue, 2),
            'refunded_revenue' => round($refundedRevenue, 2),
            'cancelled_revenue' => round($cancelledRevenue, 2),
            'net_profit' => round($netProfit, 2),
            'total_refunds' => round($totalRefunds, 2),
            'pending_refunds' => round($pendingRefunds, 2),
        ];
    }

    /**
     * Get revenue chart data
     *
     * @param string $period
     * @param string|null $type
     *
     * @return array
     */
    public function getRevenueChart(string $period = 'month', ?string $type = null): array
    {
        $query = Order::query();

        if ($type === 'project') {
            $query->where('orderable_type', Project::class);
        } elseif ($type === 'service') {
            $query->where('orderable_type', ServicePackage::class);
        }

        // Get data based on period
        switch ($period) {
            case 'week':
                // Last 12 weeks grouped by week
                $data = $query
                    ->selectRaw('YEAR(created_at) as year, WEEK(created_at) as week, MIN(DATE(created_at)) as start_date, SUM(price) as revenue')
                    ->where('created_at', '>=', now()->subWeeks(12))
                    ->groupBy('year', 'week')
                    ->orderBy('year')
                    ->orderBy('week')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'label' => 'Week ' . $item->week, // Week 1, Week 2, etc
                            'year' => $item->year,
                            'week' => $item->week,
                            'start_date' => $item->start_date,
                            'revenue' => round($item->revenue, 2),
                        ];
                    });
                break;

            case 'year':
                // Last 5 years grouped by year
                $data = $query
                    ->selectRaw('YEAR(created_at) as year, SUM(price) as revenue')
                    ->where('created_at', '>=', now()->subYears(5))
                    ->groupBy('year')
                    ->orderBy('year')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'label' => (string) $item->year, // 2022, 2023, etc
                            'year' => $item->year,
                            'revenue' => round($item->revenue, 2),
                        ];
                    });
                break;

            case 'month':
            default:
                // Last 12 months grouped by month
                $data = $query
                    ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(price) as revenue')
                    ->where('created_at', '>=', now()->subMonths(12))
                    ->groupBy('year', 'month')
                    ->orderBy('year')
                    ->orderBy('month')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'label' => date('M Y', mktime(0, 0, 0, $item->month, 1, $item->year)), // Jan 2026, Feb 2026, etc
                            'year' => $item->year,
                            'month' => $item->month,
                            'revenue' => round($item->revenue, 2),
                        ];
                    });
                break;
        }

        return [
            'period' => $period,
            'type' => $type ?? 'all',
            'data' => $data,
        ];
    }
}
