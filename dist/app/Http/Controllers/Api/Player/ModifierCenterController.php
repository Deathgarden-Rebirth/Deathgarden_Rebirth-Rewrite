<?php

namespace App\Http\Controllers\Api\Player;

use App\Http\Controllers\Controller;
use App\Http\Responses\Api\Player\ModifiersMeResponse;
use Illuminate\Support\Facades\Auth;

class ModifierCenterController extends Controller
{
    /**
     * Not jet entirely shure what the game wants.
     *
     * @return false|string
     */
    public function modifiersMe()
    {
        $user = Auth::user();

        return json_encode(new ModifiersMeResponse($user->id, $user->id));
    }
}