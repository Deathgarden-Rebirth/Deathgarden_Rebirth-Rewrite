<?php

namespace App\Http\Controllers\Api\Auth;

use App\APIClients\SteamAPIClient\ISteamUserAuth\AuthenticateTicket\AuthenticateTicketRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\SteamLoginRequest;
use App\Http\Responses\Api\Auth\SteamLoginResponse;
use App\Models\User;

class SteamAuthController extends Controller
{
    public function login(SteamLoginRequest $request)
    {
        $steamRequest = new AuthenticateTicketRequest($request->token);
        $steamResponse = $steamRequest->authenticateTicket();

        if($steamResponse === false)
            return response('Error: '.$steamRequest->getError(), 500);

        if($steamResponse->isBanned()) {
            return response('Unauthorized: You are Steam banned', 401);
        }

        $foundUser = User::firstOrCreate(['steam_id' => $steamResponse->steamId]);

        $response = new SteamLoginResponse($foundUser->id, $steamResponse->steamId);
        return json_encode($response);
    }
}