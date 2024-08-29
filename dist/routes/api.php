<?php

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
    Route::get('patch/files', [\App\Http\Controllers\Api\PatchController::class, 'getGameFileList']);
    Route::get('patch/{patchlineName}/files', [\App\Http\Controllers\Api\PatchController::class, 'getGameFileList']);
});

Route::get('online-players', [\App\Http\Controllers\Api\StatisticsController::class, 'getOnlinePlayers'])
    ->name('api.online-players');

Route::fallback(function () {
    return response('route not found', 404);
})->middleware('api.session');