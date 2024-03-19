<?php

use App\Http\Controllers\Api\Matchmaking\MatchmakingController;

Route::post('httplog/event', function () {
    return response('', 204);
});
Route::post('server/event', function () {
    return response('', 204);
});
Route::post('client/event', function () {
    return response('', 204);
});

Route::post('matchmaking/event', [MatchmakingController::class, 'cancelQueue']);