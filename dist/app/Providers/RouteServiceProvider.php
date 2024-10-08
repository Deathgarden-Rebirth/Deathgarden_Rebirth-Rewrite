<?php

namespace App\Providers;

use App\Http\Middleware\AdminPermissionCheck;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware(['api', 'api.session'])
                ->prefix('moderation')
                ->group(base_path('routes/moderation.php'));

            Route::middleware('web')
                ->withoutMiddleware(\Illuminate\Routing\Middleware\ThrottleRequests::class.':api')
                ->group(base_path('routes/web.php'));

            Route::middleware(['api', 'api.session'])
                ->prefix('api')
                ->group(base_path('routes/deathgardenApi.php'));

            Route::middleware(['api', 'api.session'])
                ->prefix('metrics')
                ->group(base_path('routes/metrics.php'));

            Route::middleware(['api', 'api.session'])
                ->group(base_path('routes/messages.php'));

            Route::middleware(['web', 'auth', AdminPermissionCheck::class])
                ->prefix('admin')
                ->group(base_path('routes/admin.php'));
        });
    }
}
