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


        $foundUser = User::firstOrCreate(['steam_id' => $steamResponse->steamId], ['source' => 'GAME']);
        Auth::login($foundUser);

        $name = static::getPlayerName($steamResponse->steamId);
        if($name !== false) {
            $foundUser->last_known_username = $name;
            $foundUser->save();
        }

        $log->info('User with SteamID "{id}" ({name}) successfully logged in.', ['id' => $steamResponse->steamId]);

        $response = new SteamLoginResponse($foundUser->id, $steamResponse->steamId);

        return json_encode($response);
    }

    protected function getPlayerName(int $steamId): string|false
    {
        $steamUserRequest = new GetPlayerSummariesRequest([$steamId]);

        $result = $steamUserRequest->getPlayerSummaries();

        if($result === false)
            return false;

        return $result->getPlayer($steamId)->personName;
    }
}