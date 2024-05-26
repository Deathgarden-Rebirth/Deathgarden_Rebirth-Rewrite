<?php

use App\Http\Controllers\Api\Auth\SteamAuthController;
use App\Http\Controllers\Api\Eula\EulaConsentController;
use App\Http\Controllers\Api\Matchmaking\MatchmakingController;
use App\Http\Controllers\Api\Player\ChallengeController;
use App\Http\Controllers\Api\Player\CurrencyController;
use App\Http\Controllers\Api\Player\InboxController;
use App\Http\Controllers\Api\Player\MetadataController;
use App\Http\Controllers\Api\Player\ModifierCenterController;
use App\Http\Controllers\Api\Player\PlayerController;
use App\Http\Controllers\Api\Player\PurchaseController;
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
    Route::get('config/MATCH_MAKING_REGIONS/raw', [MatchmakingController::class, 'getRegions']);
    Route::get('utils/contentVersion/latest/{version}', [VersionController::class, 'getLatestContentVersion'])
        ->name(VersionController::ROUTE_LATEST_CONTENT_VERSION);
    Route::get('services/tex', [VersionController::class, 'tex'])
        ->name(VersionController::ROUTE_TEX);

    Route::middleware('api.auth')->group(function () {
        Route::get('consent/eula2', [EulaConsentController::class, 'get'])->name(EulaConsentController::class);
        Route::put('consent/eula2', [EulaConsentController::class, 'put'])->name(EulaConsentController::class);

        Route::get('players/ban/status', [PlayerController::class, 'getBanStatus']);
        Route::get('modifierCenter/modifiers/me', [ModifierCenterController::class, 'modifiersMe']);

        Route::get('messages/count', [InboxController::class, 'count']);
        Route::get('messages/list', [InboxController::class, 'list']);

        Route::post('extensions/progression/initOrGetGroups', [MetadataController::class, 'initOrGetGroups']);
        Route::post('extensions/progression/updateMetadataGroup', [MetadataController::class, 'updateMetadataGroup']);

        Route::post('extensions/challenges/getChallengeProgressionBatch', [ChallengeController::class, 'getProgressionBatch']);
        Route::post('extensions/challenges/getChallenges', [ChallengeController::class, 'getChallenges']);

        Route::post('extensions/purchase/item', [PurchaseController::class, 'purchaseItem']);
        Route::post('extensions/purchase/set', [PurchaseController::class, 'purchaseSet']);

        Route::post('extensions/progression/resetCharacterProgressionForPrestige', [PlayerController::class, 'resetCharacterProgressionForPrestige']);
        Route::post('extensions/progression/playerEndOfMatch', [MatchmakingController::class, 'playerEndOfMatch']);
        Route::post('extensions/progression/endOfMatch', [MatchmakingController::class, 'endOfMatch']);
        Route::post('extensions/challenges/executeChallengeProgressionOperationBatch', [ChallengeController::class, 'executeChallengeProgressionBatch']);

        Route::post('extensions/quitters/getQuitterState', [PlayerController::class, 'getQuitterState']);

        Route::get('wallet/currencies', [CurrencyController::class, 'getCurrencies']);

        Route::get('inventories', [PlayerController::class, 'getInventory']);
        Route::post('extensions/inventory/unlockSpecialItems', [PlayerController::class, 'unlockSpecialItems']);


        Route::post('queue', [MatchmakingController::class, 'queue']);
        // Because there is no dedicated endpoint the game calls for canceling the queue, we abuse the
        // matchmaking metrics endpoint in the web.php routes file.
        Route::get('match/{matchId}', [MatchmakingController::class, 'matchInfo']);
        Route::post('match/{matchId}/register', [MatchmakingController::class, 'register']);
        Route::put('match/{matchId}/Close', [MatchmakingController::class, 'close']);
		Route::put('match/{matchId}/Kill', [MatchmakingController::class, 'kill']);
		Route::put('match/{matchId}/Quit', [MatchmakingController::class, 'quit']);
        Route::delete('match/{matchId}/user/{userId}', [MatchmakingController::class, 'deleteUserFromMatch']);
    });


    Route::post('gameDataAnalytics/batch', function () {
        return response('', 200);
    });
    Route::post('gameDataAnalytics', function () {
        return response('', 200);
    });
    Route::post('me/richPresence', function () {
        return response('', 200);
    });
});
