<?php

namespace App\Http\Controllers\Api\Player;

use App\Http\Controllers\Controller;
use App\Http\Responses\Api\Player\CurrencyResponse;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function getCurrencies()
    {
        $user = \Auth::user();

        $playerData = $user->playerData();

        $response = new CurrencyResponse(
            $playerData->currency_a,
            $playerData->currency_b,
            $playerData->currency_c,
        );

        return json_encode($response);
    }
}
