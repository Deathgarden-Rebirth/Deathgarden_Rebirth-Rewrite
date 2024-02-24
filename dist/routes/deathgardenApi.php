<?php

use App\Http\Controllers\Api\Auth\SteamAuthController;
use App\Http\Controllers\Api\Eula\EulaConsentController;
use App\Http\Controllers\Api\Player\CurrencyController;
use App\Http\Controllers\Api\Player\ModifierCenterController;
use App\Http\Controllers\Api\Player\PlayerController;
use App\Http\Controllers\Api\VersionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    Route::post('auth/provider/steam/login',[SteamAuthController::class, 'login']);

    Route::get('healthcheck', [VersionController::class, 'healthcheck'])
        ->name(VersionController::ROUTE_HEALTHCHECK);
    Route::get('config/VER_LATEST_CLIENT_DATA', [VersionController::class, 'getLatestClientData'])
        ->name(VersionController::ROUTE_LATEST_CLIENT_DATA);
    Route::get('utils/contentVersion/latest/{version}', [VersionController::class, 'getLatestContentVersion'])
        ->name(VersionController::ROUTE_LATEST_CONTENT_VERSION);
    Route::get('services/tex', [VersionController::class, 'tex'])
        ->name(VersionController::ROUTE_TEX);

    Route::middleware('api.auth')->group(function () {
        Route::get('consent/eula2', [EulaConsentController::class, 'get'])->name(EulaConsentController::class);
        Route::put('consent/eula2', [EulaConsentController::class, 'put'])->name(EulaConsentController::class);

        Route::get('players/ban/status', [PlayerController::class, 'getBanStatus']);
        Route::get('modifierCenter/modifiers/me', [ModifierCenterController::class, 'modifiersMe']);

        Route::post('extensions/progression/initOrGetGroups', [PlayerController::class, 'initOrGetGroups']);

        Route::get('wallet/currencies', [CurrencyController::class, 'getCurrencies']);

        Route::get('inventories', [PlayerController::class, 'getInventory']);
    });
});
