<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

/**
 * A class defines the Route service provider
 */
class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        Route::pattern('id', '[0-9]+');

        $this->routes(function () {
            Route::middleware(['api', 'active'])
                ->prefix('api/auth')
                ->group(base_path('routes/auth.php'));

            Route::middleware(['api','active'])
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware(['api', 'auth:api', 'administrator', 'active'])
                ->prefix('admin')
                ->group(base_path('routes/admin.php'));

            Route::middleware(['web', 'active'])
                ->group(base_path('routes/web.php'));

            Route::prefix('webhook')
                ->group(base_path('routes/webhook.php'));

        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
