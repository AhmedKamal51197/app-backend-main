<?php

namespace App\Services\Users;


use App\Http\Resources\Admin\User\UserMiniResource;
use App\Models\Role;
use App\Models\ServicePackage;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * A class defines the users service
 */
class UsersService
{
    /**
     * Get all users with analytics
     *
     * @param string $search
     * @param int $perPage
     * @param int $page
     * @param string $role
     * @param string $active
     * @param string $order_by
     *
     * @return array
     */
    public function index(string $search, int $perPage, int $page, string $role, string $active, string $order_by): array
    {
        $query = User::query()->with(['country', 'avatar', 'roles'])->withCount(['projects', 'services']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role) {
            $roleId = Role::where('name', $role)->value('id');
            if ($roleId) {
                $query->whereHas('roles', function ($q) use ($roleId) {
                    $q->where('id', $roleId);
                });
            }
        }

        if ($active !== '') {
            $query->where('active', (bool) $active);
        }

        $query->orderBy('created_at', $order_by === 'oldest' ? 'asc' : 'desc');

        $users = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'users' => $users,
            'analytics' => $this->getUserAnalytics(),
            'meta' => [
                'total_pages' => $users->lastPage(),
                'current_page' => $users->currentPage(),
                'total_items' => $users->total(),
                'per_page' => $users->perPage()
            ],
        ];
    }

    /**
     * Get all users by role
     *
     * @param string $search
     * @param string $role
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function indexByRole(string $search, string $role, int $perPage = Setting::PAGE_RESULT_LIMIT): LengthAwarePaginator
    {
        $roleId = Role::where('name', $role)->value('id');

        $query = User::query()
            ->with(['country', 'avatar'])
            ->orderByDesc('created_at')
            ->whereHas('roles', function ($query) use ($roleId) {
                $query->where('id', $roleId);
            });
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Get user analytics
     *
     * @return array
     */
    private function getUserAnalytics(): array
    {
        $adminRoles = ['admin', 'secretary', 'root'];

        return [
            'all_users' => User::count(),
            'providers' => User::whereHas('roles', fn($q) => $q->where('name', 'provider'))->count(),
            'seekers' => User::whereHas('roles', fn($q) => $q->where('name', 'seeker'))->count(),
            'admins' => User::whereHas('roles', fn($q) => $q->whereIn('name', $adminRoles))->count(),
            'active_seekers' => User::whereHas('roles', fn($q) => $q->where('name', 'seeker'))->where('active', true)->count(),
            'active_providers' => User::whereHas('roles', fn($q) => $q->where('name', 'provider'))->where('active', true)->count(),
        ];
    }


    /**
     * Get detailed analytics for a specific user
     *
     * @param User $user
     * @return array
     */
    public function getUserDetailedAnalytics(User $user): array
    {
        return [
            'orders_count' => $user->completedProjects(),
            'completed_projects' => $user->completedProjectsCount(),
            'completed_projects_budget' => $user->completedProjectsBudget(),
            'active_services' => $user->activeServicesCount(),
            'active_services_budget' => $user->activeServicesBudget(),
            'completed_services' => $user->completedServicesCount(),
            'cancelled_services' => $user->cancelledServicesCount(),
            'projects_active' => $user->activeProjectsCount(),
            'projects_pending' => $user->pendingProjectsCount(),
            'projects_cancelled' => $user->cancelledProjectsCount(),
            'platform_net_profit' => $user->platformNetProfit(),
        ];
    }

    /**
     * Show user details with related data and analytics
     *
     * @param User $user
     *
     * @return User
     */
    public function show(User $user): User
    {
        $user->loadCount(['projects', 'services', 'portfolios']);

         $user->load([
            'avatar',
            'latestKyc.country',
            'latestKyc.attachments',
            'projects' => function ($query) {
                $query->withCount('proposals');
            },
            'projects.category',
            'projects.subCategory',
            'projects.selectedProposal',
            'projects.selectedProposal.user',
            'paymentRequests',
            'reports',
            'roles',
            'category',
            'userSubCategories.subCategory',
            'userSkills.skill',
            'workedProjects' => function ($query) {
                $query->withCount('proposals');
            },
            'workedProjects.user',
            'workedProjects.category',
            'workedProjects.selectedProposal',
            'portfolios.category',
            'portfolios.subCategory',
            'portfolios.attachments',
            'portfolios.skills.skill'
        ]);

        $user->ordered_services = $user->orderedServices()->get();

        $this->loadServicesWithSalesData($user);

        $user->analytics = $this->getUserDetailedAnalytics($user);

        return $user;
    }

    /**
     * Load services with sales data
     *
     * @param User $user
     * @return void
     */
    private function loadServicesWithSalesData(User $user): void
    {
        $user->load([
            'services' => function ($query) {
                $query->with(['category', 'subCategory'])
                ->withCount([
                    'packages as purchase_count' => function ($q) {
                        $q->join('orders', 'orders.orderable_id', '=', 'service_packages.id')
                          ->where('orders.orderable_type', ServicePackage::class)
                          ->whereIn('orders.status', ['completed', 'released']);
                    }
                ])->withSum([
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
            }
        ]);

        // Ensure total_revenue is 0 when purchase_count is 0
        $user->services->each(function ($service) {
            $service->total_revenue = $service->total_revenue ?? 0;
        });
    }

    /**
     * Toggle user's active status
     *
     * @param User $user
     *
     * @return User
     */
    public function toggleActive(User $user): User
    {
        $user->update(['active' => !$user->active]);

        return $user;
    }

    /**
     * Get users chart data
     *
     * @param string $period
     * @param string|null $role
     *
     * @return array
     */
    public function getChart(string $period = 'month', ?string $role = null): array
    {
        $query = User::query();
        if ($role) {
            $query->whereHas('roles', fn($q) => $q->where('name', $role));
        }

        if ($period === 'week') {
            $data = (clone $query)
                ->selectRaw('YEAR(created_at) as year, WEEK(created_at) as week, COUNT(*) as count')
                ->where('created_at', '>=', now()->subWeeks(12))
                ->groupBy('year', 'week')
                ->orderBy('year')
                ->orderBy('week')
                ->get()
                ->map(fn($item) => ['period' => "Week {$item->week}, {$item->year}", 'count' => $item->count]);
        } else {
            [$dateFormat, $timeFilter] = match($period) {
                'year' => ['YEAR(created_at)', now()->subYears(5)],
                default => ['DATE_FORMAT(created_at, "%Y-%m")', now()->subMonths(12)]
            };

            $data = (clone $query)
                ->selectRaw("$dateFormat as period, COUNT(*) as count")
                ->where('created_at', '>=', $timeFilter)
                ->groupBy('period')
                ->orderBy('period')
                ->get()
                ->map(fn($item) => ['period' => $item->period, 'count' => $item->count]);
        }

        return ['total' => $data->sum('count'), 'data' => $data];
    }

    /**
     * Get users status analytics
     *
     * @return array
     */
    public function getStatusAnalytics(): array
    {
        return [
            'seekers' => [
                'total' => User::whereHas('roles', fn($q) => $q->where('name', 'seeker'))->count(),
                'active' => User::whereHas('roles', fn($q) => $q->where('name', 'seeker'))->where('active', true)->count(),
                'inactive' => User::whereHas('roles', fn($q) => $q->where('name', 'seeker'))->where('active', false)->count(),
            ],
            'providers' => [
                'total' => User::whereHas('roles', fn($q) => $q->where('name', 'provider'))->count(),
                'active' => User::whereHas('roles', fn($q) => $q->where('name', 'provider'))->where('active', true)->count(),
                'inactive' => User::whereHas('roles', fn($q) => $q->where('name', 'provider'))->where('active', false)->count(),
            ],
        ];
    }

    /**
     * Get top spenders and earners
     *
     * @param int $limit
     *
     * @return array
     */
    public function getTopUsers(): array
    {
        $topLimit = 5;
        $topSeekers = User::whereHas('roles', fn($q) => $q->where('name', 'seeker'))
            ->withCount(['seekerOrders as orders_count' => fn($q) => $q->whereIn('status', ['completed', 'released'])])
            ->withSum(['seekerOrders as total_spending' => fn($q) => $q->whereIn('status', ['completed', 'released'])], 'price')
            ->withCount(['projects'])
            ->with('avatar')
            ->having('total_spending', '>', 0)
            ->orderByDesc('total_spending')
            ->limit($topLimit)
            ->get();

        $topProviders = User::whereHas('roles', fn($q) => $q->where('name', 'provider'))
            ->withCount(['providerOrders as orders_count' => fn($q) => $q->whereIn('status', ['completed', 'released'])])
            ->withSum(['providerOrders as total_income' => fn($q) => $q->whereIn('status', ['completed', 'released'])], 'price')
            ->withCount(['projects', 'services'])
            ->with('avatar')
            ->having('total_income', '>', 0)
            ->orderByDesc('total_income')
            ->limit($topLimit)
            ->get();

        return [
            'top_seekers' => UserMiniResource::collection($topSeekers),
            'top_providers' => UserMiniResource::collection($topProviders)
        ];
    }

    /**
     * Get complete users analytics
     *
     * @param string $period
     * @param string|null $role
     *
     * @return array
     */
    public function usersAnalytics(string $period = 'month', ?string $role = null): array
    {
        return [
            'chart' => $this->getChart($period, $role),
            'status' => $this->getStatusAnalytics(),
            'top_users' => $this->getTopUsers()
        ];
    }
}
