<?php

namespace App\Http\Controllers\Api\Player;

use App\Http\Controllers\Controller;
use App\Http\Responses\Api\Player\GetBanStatusResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlayerController extends Controller
{
    public function getBanStatus()
    {
        $user = Auth::user();

        if($user->ban === null)
            return json_encode(new GetBanStatusResponse(false));

        return json_encode(new GetBanStatusResponse(true, $user->ban));
    }
}
