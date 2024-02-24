<?php

namespace App\Http\Controllers\Api\Player;

use App\Enums\Game\ItemGroupType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Player\GetInventoryRequest;
use App\Http\Requests\Api\Player\InitOrGetGroupsRequest;
use App\Http\Responses\Api\Player\GetBanStatusResponse;
use App\Http\Responses\Api\Player\InitOrGetGroupsResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

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

            if (!$request->skipMetadataGroups) {
                if($group->getCharacter() !== false)
                    $response->addCharacterMetadataGroup($group->getCharacter(), $user);
            }
        }

        $response->addGeneralMetadata($user);

        return json_encode($response);
    }

    public function getInventory(GetInventoryRequest $request)
    {
        $user = Auth::user();
        $playerData = $user->playerData();

        $inventoryItems = $playerData->inventory;
        $inventoryData = [];

        foreach ($inventoryItems as $item) {
            $inventoryData[] = [
                'quantity' => 1,
                'objectId' => Uuid::fromString($item->id)->getHex()->toString(),
                'lastUpdateAt' => $item->pivot->updated_at->getTimestamp(),
            ];
        }

        $response = [
            'code' => 200,
            'message' => 'OK',
            'data' => [
                'playerId' => $user->id,
                'inventory' => $inventoryData,
            ],
        ];

        return json_encode($response);

    }

}
