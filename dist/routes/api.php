<?php

use App\Http\Controllers\Api\PatchController;
use App\Http\Controllers\Api\StatisticsController;

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
    Route::get('patch/files', [PatchController::class, 'getGameFileList']);
    Route::get('patch/{patchlineName}/files', [PatchController::class, 'getGameFileList']);

    Route::get('launcher-version', [StatisticsController::class, 'getLauncherVersion']);
});

Route::get('online-players', [StatisticsController::class, 'getOnlinePlayers'])
    ->name('api.online-players');

Route::fallback(function () {
    return response('route not found', 404);
})->middleware('api.session');