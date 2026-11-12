<?php

use App\Http\Middleware\AccessLogger;
use App\Http\Middleware\AdminPermissionCheck;
use App\Http\Middleware\VerifyMigrationKey;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('api')->prefix('api')->group(base_path('routes/api.php'));
            Route::middleware(['api', 'api.session'])->prefix('moderation')->group(base_path('routes/moderation.php'));
            Route::middleware('web')->group(base_path('routes/web.php'));
            Route::middleware(['api', 'api.session'])->prefix('api')->group(base_path('routes/deathgardenApi.php'));
            Route::middleware(['api', 'api.session'])->prefix('metrics')->group(base_path('routes/metrics.php'));
            Route::middleware(['api', 'api.session'])->group(base_path('routes/messages.php'));
            Route::middleware(['web', 'auth', AdminPermissionCheck::class])->prefix('admin')->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->group('web', [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            AccessLogger::class,
            ShareErrorsFromSession::class,
            PreventRequestForgery::class,
            SubstituteBindings::class,
        ]);

        $middleware->api(append: AccessLogger::class);

        $middleware->group('api.session', [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            AccessLogger::class,
        ]);

        $middleware->group('api.auth', [
            'auth',
            'auth.session',
        ]);

        $middleware->alias([
            'verify_migration_key' => VerifyMigrationKey::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // TODO: New error responses by Laravel for API - not tested yet
        // $exceptions->shouldRenderJsonWhen(
        //     fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        // );

        $exceptions->stopIgnoring([HttpException::class, HttpResponseException::class]);

        $exceptions->report(function (Throwable $e) {
            $request = request();
            $log = new stdClass;
            $log->method = $request->method();
            $log->url = $request->fullUrl();
            $log->headers = $request->headers->all();
            $log->queryParams = $request->getQueryString();
            $log->body = $request->all();
            $log->error = new stdClass;
            $log->error->message = $e->getMessage();
            $log->error->file = $e->getFile();
            $log->error->line = $e->getLine();
            $log->error->trace = $e->getTrace();

            Log::stack(['dg_requests_errors'])->error($log->method.' '.$log->url."\n".json_encode($log, JSON_PRETTY_PRINT));
        });
    })->create();
