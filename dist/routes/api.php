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

use App\Http\Controllers\Api\PatchController;

Route::prefix('files')->group(function () {
    Route::get('patch/current', [PatchController::class, 'getCurrentPatch']);
    Route::get('patch/sig', [PatchController::class, 'getSignature']);
    Route::get('patch/battleye', [PatchController::class, 'getBattleyePatch']);
});

Route::get('patch/files', [PatchController::class, 'getGameFileList']);

Route::prefix('v1')->middleware('api.session')->group(function () {
    Route::get('profile', function() {
        if (!Auth::check())
            return response()->json("Unauthorized", 401);
        return response()->json(Auth::user());
    });
});

Route::get('online-players', [\App\Http\Controllers\Api\StatisticsController::class, 'getOnlinePlayers'])
    ->name('api.online-players');

Route::fallback(function () {
    return response('route not found', 404);
})->middleware('api.session');