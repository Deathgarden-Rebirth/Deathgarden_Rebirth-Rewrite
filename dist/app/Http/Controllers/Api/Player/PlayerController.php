<?php

namespace App\Http\Controllers\Api\Player;

use App\Classes\Config\SpecialUnlocksItemsConfig;
use App\Enums\Auth\Permissions;
use App\Enums\Game\RewardType;
use App\Helper\Uuid\UuidHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Player\GetInventoryRequest;
use App\Http\Requests\Api\Player\ResetCharacterProgressionForPrestigeRequest;
use App\Http\Requests\Api\Player\UnlockSpecialitemsRequest;
use App\Http\Responses\Api\General\Reward;
use App\Http\Responses\Api\Player\GetBanStatusResponse;
use App\Http\Responses\Api\Player\GetQuitterStateResponse;
use App\Http\Responses\Api\Player\ResetCharacterProgressionForPrestigeResponse;
use App\Models\Game\CatalogItem;
use App\Models\Game\Challenge;
use App\Models\Game\CharacterData;
use App\Models\Game\PickedChallenge;
use App\Models\Game\PrestigeReward;
use App\Models\Game\QuitterState;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class PlayerController extends Controller
{
    public function getBanStatus()
    {
        $user = Auth::user();

        $banCheckQuery = $user->bans()
            ->where('start_date', '<', Carbon::now()->toDateTimeString())
            ->where('end_date', '>', Carbon::now()->toDateTimeString());

        if (!$banCheckQuery->exists())
            return json_encode(new GetBanStatusResponse(false));

        return json_encode(new GetBanStatusResponse(true, $banCheckQuery->first()));
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

    public function unlockSpecialItems(UnlockSpecialitemsRequest $request)
    {
        if(count($request->appIds) === 0)
            return ['unlockedItems' => []];

        $user = Auth::user();

        $itemsToCheck = [];

        foreach ($request->appIds as $appid) {
            $itemsToCheck = [...$itemsToCheck, ...SpecialUnlocksItemsConfig::getFromAppId($appid)];
        }

        if($user->playerData()->has_played_dg_1)
            $itemsToCheck = [...$itemsToCheck, ...SpecialUnlocksItemsConfig::LEGACY, ...SpecialUnlocksItemsConfig::BETA];

        $itemsToCheck = UuidHelper::convertFromHexToUuidCollecton($itemsToCheck, true);

        $foundItems = $user->playerData()->inventory()->allRelatedIds()->intersect($itemsToCheck);
        if($foundItems->count() === count($itemsToCheck))
            return ['unlockedItems' => []];

        $itemsToAdd = $itemsToCheck->diff($foundItems);
        $user->playerData()->inventory()->syncWithoutDetaching($itemsToAdd);

        return json_encode(['unlockedItems' => UuidHelper::convertFromUuidToHexCollection($itemsToAdd)]);
    }

    public function getQuitterState()
    {
        $quitterState = Auth::user()->playerData()->quitterState;

        $quitterState->checkStrikeRefresh();

        $response = new GetQuitterStateResponse(
            $quitterState->strike_refresh_time === null ? 0 : Carbon::parse($quitterState->strike_refresh_time)->getTimestamp(),
            $quitterState->strikes_left,
            $quitterState->stay_match_streak,
            $quitterState->stay_match_streak_previous,
            $quitterState->has_quit_once,
            $quitterState->quit_match_streak,
            $quitterState->quit_match_streak_previous,
        );

        $response->stayMatchStreakRewards = QuitterState::getReward($quitterState->stay_match_streak);

        return json_encode($response);
    }

    public function resetCharacterProgressionForPrestige(ResetCharacterProgressionForPrestigeRequest $request)
    {
        $user = Auth::user();
        $playerData = $user->playerData();
        $characterData = $playerData->characterDataForCharacter($request->character);
        $characterCatalogItem = CatalogItem::find(Uuid::fromString($request->characterCatalogId)->toString());

        if(!$this->checkPrestigePrerequisites($characterData, $characterCatalogItem))
            return json_encode(ResetCharacterProgressionForPrestigeResponse::createAbortResponse($characterData->prestige_level));

        $this->resetCharacter($characterData);

        /** @var PrestigeReward $prestigeReward */
        $prestigeReward = $characterCatalogItem->prestigeRewards[$characterData->prestige_level];

        $playerData->currency_a -= $prestigeReward->cost_currency_a;
        $playerData->currency_b -= $prestigeReward->cost_currency_b;
        $playerData->currency_c -= $prestigeReward->cost_currency_c;

        $rewardIdsToAdd = [];
        $responseRewards = [];

        foreach ($prestigeReward->rewardItems as $rewardItem) {
            $rewardIdsToAdd[] = $rewardItem->catalog_item_id;
            $responseRewards[] = new Reward(
                RewardType::Inventory,
                1,
                $rewardItem->catalog_item_id,
            );
        }

        try {
            $playerData->inventory()->attach($rewardIdsToAdd);
        }
        catch (UniqueConstraintViolationException $e) {}

        ++$characterData->prestige_level;

        $playerData->save();
        $characterData->save();

        $response = new ResetCharacterProgressionForPrestigeResponse();
        $response->prestigelevel = $characterData->prestige_level;
        $response->rewards = $responseRewards;

        return json_encode($response);
    }

    protected function resetCharacter(CharacterData &$characterData): void
    {
        $config = $characterData->character->getCharacter()->getItemConfigClass();
        $allItemsToRemove = CatalogItem::find(UuidHelper::convertFromHexToUuidCollecton($config::getAllItems(), true));
        $defaultItems = UuidHelper::convertFromHexToUuidCollecton([
            ...$config::getDefaultEquipment(),
            ...$config::getDefaultEquippedPerks(),
            ...$config::getDefaultPowers(),
            ...$config::getDefaultEquippedWeapons(),
            ...$config::getDefaultEquippedBonuses(),
        ], true);
        $inventory = $characterData->playerData->inventory();

        // first detach all character items
        $inventory->detach($allItemsToRemove);
        //then attach the defaults again
        $inventory->attach([...$defaultItems, $config::getCharacterId()->toString()]);

        // Remove signature challenges
        foreach ($allItemsToRemove as $item) {
            /** @var Challenge[]|Collection $challenges */
            $challenges = $item->requiredChallenges;

            foreach ($challenges as $challenge){
                $challenge->playerData()->detach($characterData->playerData->id);
            }
        }

        // Remove all picked Challenges
        $characterData->pickedChallenges->each(function (PickedChallenge $challenge) {
            $challenge->delete();
        });

        // reset experience and level
        $characterData->experience = 0;
        $characterData->level = 1;
    }

    /**
     * Checks if the player has the required items in his inventory (Lv. 10) and if he has enough currency
     *
     * @param CharacterData $characterData
     * @param CatalogItem $characterItem
     * @return bool Whether the player is allowed to prestige the character or not
     */
    protected function checkPrestigePrerequisites(CharacterData $characterData, CatalogItem $characterItem): bool
    {
        if($characterData->prestige_level >= 4)
            return false;

        $config = $characterData->character->getCharacter()->getItemConfigClass();
        $allCharacterItems = CatalogItem::find(UuidHelper::convertFromHexToUuidCollecton($config::getAllItems(), true));

        // Because prestige should only be possible if you have all lvl 10 items for the character,
        // we check if all of these level 10 items are in our inventory.
        /** @var CatalogItem[]|Collection $itemsToCheck */
        $itemsToCheck = $allCharacterItems->filter(function (CatalogItem $item) {
            return Str::contains($item->display_name, '_010_');
        });

        $missingItem = false;

        foreach ($itemsToCheck as $item) {
            if ($characterData->playerData->inventory()->find($item->id) === null)
                $missingItem = true;
        }

        if($missingItem)
            return false;

        $playerData = $characterData->playerData;
        $prestigeReward = $characterItem->prestigeRewards[$characterData->prestige_level];
        // Check if we can afford the prestige, if not return false;
        if( $playerData->currency_a < $prestigeReward->cost_currency_a ||
            $playerData->currency_b < $prestigeReward->cost_currency_b ||
            $playerData->currency_c < $prestigeReward->cost_currency_c)
            return false;

        return true;
    }
}
