<?php

namespace App\Http\Controllers\Api\Player;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Player\PurchaseItemRequest;
use App\Http\Responses\Api\Player\Purchase\PurchaseItemResponse;
use App\Http\Responses\Api\Player\Purchase\WalletEntry;
use App\Models\Game\CatalogItem;
use App\Models\Game\Challenge;
use App\Models\Game\CharacterData;
use App\Models\Game\PickedChallenge;
use App\Models\User\PlayerData;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

class PurchaseController extends Controller
{
    public function purchaseItem(PurchaseItemRequest $request)
    {
        $user = Auth::user();
        $playerData = $user->playerData();
        $quantityToBuy = $request->wantedQuantity - $request->oldQuantity;

        $itemToBuy = CatalogItem::find($request->objectId);

        if(!$this->hasCompletedChallengeForItem($itemToBuy, $playerData))
            return response('Not completed required challenge/s', 418);

        if( $playerData->currency_a < ($itemToBuy->default_cost_currency_a * $quantityToBuy) ||
            $playerData->currency_b < ($itemToBuy->default_cost_currency_b * $quantityToBuy) ||
            $playerData->currency_c < ($itemToBuy->default_cost_currency_c * $quantityToBuy))
            return response('Not enough Currency', 418);

        try {
            $playerData->inventory()->attach($itemToBuy);
        }
        catch (UniqueConstraintViolationException $e) {
            return response('Item already in inventory.', 418);
        }

        $playerData->currency_a -= $itemToBuy->default_cost_currency_a * $quantityToBuy;
        $playerData->currency_b -= $itemToBuy->default_cost_currency_b * $quantityToBuy;
        $playerData->currency_c -= $itemToBuy->default_cost_currency_c * $quantityToBuy;
        $playerData->save();

        $hexItemId = Uuid::fromString($itemToBuy->id)->getHex()->toString();

        $response = new PurchaseItemResponse(
            $user->id,
            $hexItemId,
            $quantityToBuy,
            new WalletEntry(
                $playerData->currency_a,
                $itemToBuy->default_cost_currency_a * $quantityToBuy,
                $playerData->currency_b,
                $itemToBuy->default_cost_currency_b * $quantityToBuy,
                $playerData->currency_c,
                $itemToBuy->default_cost_currency_c * $quantityToBuy,
            ),
            [$hexItemId],
        );

        return json_encode($response);
    }

    protected function hasCompletedChallengeForItem(CatalogItem &$item, PlayerData &$playerData)
    {
        /** @var Challenge $foundChallenge */
        $foundChallenge = $item->requiredChallenges()->first();

        if ($foundChallenge !== null) {
            return $foundChallenge->getProgressForPlayer($playerData->id) >= $foundChallenge->completion_value;
        }

        $characterIds = [];
        $playerData->characterData()->get(['id'])->each(function (CharacterData $data) use ($characterIds) {
            $characterIds[] = $data->id;
        });

        $foundChallenge = PickedChallenge::where('catalog_item_id', '=', $item->id)
            ->whereIn('character_data_id', $characterIds)->first();

        if($foundChallenge === null)
            return true;

        return $foundChallenge->progress >= $foundChallenge->completion_value;
    }
}
