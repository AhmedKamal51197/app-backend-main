<?php

namespace App\Providers;

use App\Models\Kyc;
use App\Models\Order;
use App\Models\PaymentRequest;
use App\Models\Project;
use App\Models\RefundRequest;
use App\Models\Report;
use App\Models\Service;
use App\Observers\DashboardNotificationObserver;
use App\Observers\OrderObserver;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

/**
 * A class defines the app service provider
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(TelescopeServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        Order::observe(OrderObserver::class);

        foreach ([Project::class, Service::class, Order::class, RefundRequest::class, PaymentRequest::class, Report::class, Kyc::class] as $model) {
            $model::observe(DashboardNotificationObserver::class);
        }
    }
}
