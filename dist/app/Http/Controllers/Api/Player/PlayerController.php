<?php

namespace App\Http\Controllers\Api\Player;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Player\GetInventoryRequest;
use App\Http\Responses\Api\Player\GetBanStatusResponse;
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
