<?php

namespace App\Services\Service;

use App\Actions\Categories\CategoriesAveragePricesAction;
use App\Enums\OrderDirectionEnum;
use App\Models\Commission;
use App\Models\Order;
use App\Models\Service;
use App\Models\ServicePackage;

/**
 * A class defines the admin serviceService
 */
class AdminServiceService
{
    public function __construct(
        private readonly CategoriesAveragePricesAction $categoriesAction
    ) {}

    /**
     * List services with analytics
     *
     * @param array $data
     *
     * @return array
     */
    public function index(array $data): array
    {
        $query = Service::with(['user', 'category', 'subCategory'])
            ->withSum([
                'packages as total_revenue' => function ($q) {
                    $q->join('orders', 'orders.orderable_id', '=', 'service_packages.id')
                      ->where('orders.orderable_type', ServicePackage::class)
                      ->whereIn('orders.status', ['completed', 'released']);
                }
            ], 'orders.price')
            ->addSelect([
                'first_package_price' => ServicePackage::selectRaw('price')
                    ->whereColumn('service_id', 'services.id')
                    ->orderBy('id')
                    ->limit(1)
            ]);

        if (!empty($data['search'])) {
            $query->where(function ($q) use ($data) {
                $q->where('title', 'like', "%{$data['search']}%")
                  ->orWhere('description', 'like', "%{$data['search']}%");
            });
        }

        if (isset($data['status'])) {
            $query->where('is_enabled', (bool) $data['status']);
        }

        $orderDirection = $data['order_by'] === OrderDirectionEnum::OLDEST->value ? 'asc' : 'desc';
        $query->orderBy('created_at', $orderDirection);

        $services = $query->paginate(perPage: $data['limit'], page: $data['page']);

        // Ensure total_revenue is 0 when null
        $services->getCollection()->each(function ($service) {
            $service->total_revenue = $service->total_revenue ?? 0;
        });

        $this->loadSeekersForServices($services->getCollection());

        return [
            'services' => $services,
            'analytics' => $this->getAnalytics($data['services_categories_rate']),
            'meta' => [
                'total_pages' => $services->lastPage(),
                'current_page' => $services->currentPage(),
                'total_items' => $services->total(),
                'per_page' => $services->perPage()
            ],
        ];
    }

    /**
     * Load seekers for services efficiently
     */
    private function loadSeekersForServices($services): void
    {
        if ($services->isEmpty()) {
            return;
        }

        $serviceIds = $services->pluck('id');

        // Get all orders with seekers for these services
        $orders = Order::whereHasMorph('orderable', [ServicePackage::class], function ($query) use ($serviceIds) {
            $query->whereIn('service_id', $serviceIds);
        })
        ->with(['seeker', 'orderable'])
        ->get();

        // Group orders by service_id
        $ordersByService = $orders
            ->filter(fn($order) => $order->orderable !== null)
            ->groupBy(fn($order) => $order->orderable->service_id);

        // Attach seekers to each service
        $services->each(function ($service) use ($ordersByService) {
            $serviceOrders = $ordersByService->get($service->id, collect());
            $service->seekers = $serviceOrders
                ->pluck('seeker')
                ->filter()
                ->unique('id')
                ->values();
        });
    }

    /**
     * Get services analytics
     *
     * @param string $servicesCategoriesRate
     * @return array
     */
    public function getAnalytics(string $servicesCategoriesRate): array
    {
        $allTimeQuery = Service::query();
        $enabledServices = (clone $allTimeQuery)->where('is_enabled', true)->count();
        $disabledServices = (clone $allTimeQuery)->where('is_enabled', false)->count();

        $avgPrice = ServicePackage::query()->avg('price') ?? 0;

        $ordersByStatus = $this->getOrdersByStatus();
        $completedOrders = $ordersByStatus['completed'] ?? 0;
        $cancelledOrders = $ordersByStatus['cancelled'] ?? 0;
        $totalOrders = $completedOrders + $cancelledOrders;

        return [
            'enabled_services' => $enabledServices,
            'total_services' => $this->getTotalOrderedServices(),
            'active_services' => $ordersByStatus['in_progress'] ?? 0,
            'pending_services' => $ordersByStatus['approval_pending'] ?? 0,
            'completed_services' => $completedOrders,
            'cancelled_services' => $cancelledOrders,
            'cancel_pending_services' => $ordersByStatus['cancel_pending'] ?? 0,
            'released_services' => $ordersByStatus['released'] ?? 0,
            'disputed_services' => $ordersByStatus['disputed'] ?? 0,
            'rejected_services' => $ordersByStatus['rejected'] ?? 0,
            'refunded_services' => $ordersByStatus['refunded'] ?? 0,
            'revision_services' => $ordersByStatus['revision'] ?? 0,
            'release_pending_services' => $ordersByStatus['release_pending'] ?? 0,
            'disabled_services' => $disabledServices,
            'hidden_services' => (clone $allTimeQuery)->where('hidden', true)->count(),
            'complete_vs_cancelled' => $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100) : 0,
            'completion_average' => $this->getCompletionAverage(),
            'delivery_time_average' => $this->getDeliveryTimeAverage(),
            'price_average' => round($avgPrice, 2),
            'revenue' => $this->getRevenue(),
            'profit' => $this->getProfit(),
            'services_categories' => $this->categoriesAction->calculate(new Service(), $servicesCategoriesRate)
        ];
    }

    /**
     * Get orders by status for services
     */
    private function getOrdersByStatus(): array
    {
        return Order::whereHas('orderable', function ($query) {
            $query->where('orderable_type', ServicePackage::class);
        })
        ->selectRaw('status, COUNT(*) as count')
        ->groupBy('status')
        ->pluck('count', 'status')
        ->toArray();
    }

    /**
     * Get orders by status for services
     */
    private function getTotalOrderedServices(): int
    {
        return Order::whereHas('orderable', function ($query) {
            $query->where('orderable_type', ServicePackage::class);
        })
        ->count();
    }

    /**
     * Get revenue from service orders
     */
    public function getRevenue(): float
    {
        return Order::whereHas('orderable', function ($query) {
            $query->where('orderable_type', ServicePackage::class);
        })
        ->sum('price') ?? 0;
    }

    /**
     * Get profit from service commissions
     */
    private function getProfit(): float
    {
        return Commission::whereHasMorph('payable', [Order::class], function ($query) {
            $query->whereHas('orderable', function ($q) {
                $q->where('orderable_type', ServicePackage::class);
            });
        })->sum('amount') ?? 0;
    }

    /**
     * Get completion average percentage
     */
    private function getCompletionAverage(): int
    {
        $orderStats = Order::whereHas('orderable', function ($query) {
            $query->where('orderable_type', ServicePackage::class);
        })
        ->selectRaw('COUNT(*) as total, SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed')
        ->first();

        if (!$orderStats) {
            return 0;
        }

        $total = $orderStats->total ?? 0;
        $completed = $orderStats->completed ?? 0;

        return $total > 0 ? round(($completed / $total) * 100) : 0;
    }

    /**
     * Get average delivery time in days
     */
    private function getDeliveryTimeAverage(): int
    {
        $completedOrders = Order::whereHas('orderable', function ($query) {
            $query->where('orderable_type', ServicePackage::class);
        })
        ->where('status', 'completed')
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
     * Get analytics for a specific service
     *
     * @param Service $service
     *
     * @return array
     */
    public function getServiceAnalytics(Service $service): array
    {
        $servicePackageIds = $service->packages->pluck('id');

        $orders = Order::whereHasMorph('orderable', [ServicePackage::class], function ($query) use ($servicePackageIds) {
            $query->whereIn('id', $servicePackageIds);
        })->get();

        $uniqueBuyers = $orders->pluck('seeker_id')->unique()->count();

        $totalOrders = $orders->count();

        $totalRevenue = $orders->sum('price');

        $orderIds = $orders->pluck('id');

        $platformProfit = Commission::whereIn('payable_id', $orderIds)
            ->where('payable_type', Order::class)
            ->sum('amount') ?? 0;

        $userProfit = $totalRevenue - $platformProfit;

        return [
            'buyers_count' => $uniqueBuyers,
            'orders_count' => $totalOrders,
            'total_revenue' => $totalRevenue,
            'user_profit' => round($userProfit, 2),
            'platform_profit' => round($platformProfit, 2),
        ];
    }
}
