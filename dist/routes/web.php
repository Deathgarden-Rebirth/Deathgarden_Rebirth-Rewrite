<?php

use App\Http\Controllers\Api\Matchmaking\MatchmakingController;
use App\Http\Controllers\Web\LoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/auth/redirect', [LoginController::class, 'redirect'])->name(LoginController::ROUTE_LOGIN);

Route::get('/auth/logout', function () {
    \Illuminate\Support\Facades\Auth::logout();
    return redirect('/');
})->name('logout');

Route::get('/auth/callback', [LoginController::class, 'callback'])->name(LoginController::ROUTE_CALLBACK);
Route::get('/auth/launcherCallback', [LoginController::class, 'launcherCallback']);

Route::get('{catalogVersion}/catalog', [\App\Http\Controllers\Api\Catalog\CatalogController::class, 'getCatalog']);

Route::get('/', function () {
    return \Inertia\Inertia::render('Dashboard');
});

Route::middleware('verify_migration_key')->get('/migrate-database', function () {
    Artisan::call('migrate --no-interaction');
    print Artisan::output();
    Artisan::call('db:seed');
    print Artisan::output();
    Artisan::call('optimize:clear');
    print Artisan::output();
    Artisan::call('vendor:publish --tag=log-viewer-assets --force');
    print Artisan::output();
});


Route::post('file/{gameVersion}/{seed}/{mapName}', [MatchmakingController::class, 'seedFilePost']);
Route::get('file/{gameVersion}/{seed}/{mapName}', [MatchmakingController::class, 'seedFileGet']);
