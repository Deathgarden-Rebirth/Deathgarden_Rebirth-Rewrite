<?php

namespace App\Http\Controllers\Api\Auth;

use App\APIClients\SteamAPIClient\ISteamUser\GetPlayerSummaries\GetPlayerSummariesRequest;
use App\APIClients\SteamAPIClient\ISteamUserAuth\AuthenticateTicket\AuthenticateTicketRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\SteamLoginRequest;
use App\Http\Responses\Api\Auth\SteamLoginResponse;
use App\Models\User\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SteamAuthController extends Controller
{
    public function login(SteamLoginRequest $request)
    {
        $log = Log::stack(['stack', 'login']);

        $steamRequest = new AuthenticateTicketRequest($request->token);
        $steamResponse = $steamRequest->authenticateTicket();

        if($steamResponse === false) {
            $log->alert('Authenticating session ticket failed: {error}.', ['error' => $steamRequest->getError()]);
            return response('Error: '.$steamRequest->getError(), 500);
        }

        if($steamResponse->isBanned()) {
            $log->notice('User with SteamID "{id}" denied login. User is Banned via Steam', ['id' => $steamResponse->steamId]);
            return response('Unauthorized: You are Steam banned', 401);
        }

        $foundUser = User::firstOrCreate(['steam_id' => $steamResponse->steamId], ['source' => 'GAME', 'steam_id' => $steamResponse->steamId]);
        Auth::login($foundUser);

        $this->fillSteamData($foundUser);

        $log->info('User with SteamID "{id}" ({name}) successfully logged in.', ['id' => $steamResponse->steamId, 'name' => $foundUser->last_known_username]);

        $response = new SteamLoginResponse($foundUser->id, $steamResponse->steamId);

        return json_encode($response);
    }

    /**
     * Fills in some steam data like the name and avatar url's
     *
     * @param User $user
     * @return void
     */
    protected function fillSteamData(User &$user): void
    {
        $steamUserRequest = new GetPlayerSummariesRequest([$user->steam_id]);
        $result = $steamUserRequest->getPlayerSummaries();

        if($result === false)
            return;

        $player = $result->getPlayer($user->steam_id);

        $user->last_known_username = $player->personName;
        $user->avatar_small = $player->avatar;
        $user->avatar_medium = $player->avatarMedium;
        $user->avatar_full = $player->avatarFull;
    }
}