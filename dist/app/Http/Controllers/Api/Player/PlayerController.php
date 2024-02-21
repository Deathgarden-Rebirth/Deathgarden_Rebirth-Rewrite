<?php

namespace App\Http\Controllers\Api\Player;

use App\Enums\Game\ItemGroupType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Player\InitOrGetGroupsRequest;
use App\Http\Responses\Api\Player\GetBanStatusResponse;
use App\Http\Responses\Api\Player\InitOrGetGroupsResponse;
use App\Http\Responses\Api\Player\PlayerData;
use App\Http\Responses\Api\Player\SplinteredState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlayerController extends Controller
{
    public function getBanStatus()
    {
        $user = Auth::user();

        if ($user->ban === null)
            return json_encode(new GetBanStatusResponse(false));

        return json_encode(new GetBanStatusResponse(true, $user->ban));
    }

    public function initOrGetGroups(InitOrGetGroupsRequest $request)
    {
        $response = new InitOrGetGroupsResponse();
        $user = Auth::user();

        foreach (ItemGroupType::cases() as $group) {
            if ($group == ItemGroupType::None)
                continue;

            if (!$request->skipProgressionGroups) {
                if ($group->getCharacter() !== false)
                    $response->addCharacterProgressionGroup($group->getCharacter(), $user);
                else
                    $response->addFactionProgression($group, $user);
            }
        }

        return json_encode($response);
    }

}
