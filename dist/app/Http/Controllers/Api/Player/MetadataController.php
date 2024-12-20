<?php

namespace App\Http\Controllers\Api\Player;

use App\Enums\Game\Faction;
use App\Enums\Game\Hunter;
use App\Enums\Game\ItemGroupType;
use App\Enums\Game\MetadataGroup;
use App\Enums\Game\Runner;
use App\Helper\Uuid\UuidHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Player\InitOrGetGroupsRequest;
use App\Http\Requests\Api\Player\UpdateMetadataGroupRequest;
use App\Http\Responses\Api\Player\InitOrGetGroupsResponse;
use App\Http\Responses\Api\Player\UpdateMetadataResponse;
use App\Models\Game\PickedChallenge;
use App\Models\User\User;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Uuid;

class MetadataController extends Controller
{

    /**
     * Mapping from
     */
    const PICKED_CHALLENGE_REDUCTION_MAPPING = [
        '/Game/Challenges/Progression/General/Challenge_Downing_Hunter.Challenge_Downing_Hunter' => 74.49,
        '/Game/Challenges/Progression/General/Challenge_Execution_Hunter.Challenge_Execution_Hunter' => 76.19,
        '/Game/Challenges/Progression/General/Challenge_Ressources_Runner.Challenge_Ressources_Runner' => 58.05,
        '/Game/Challenges/Progression/General/Challenge_Travel_Hunter.Challenge_Travel_Hunter' => 52.38,
        '/Game/Challenges/Progression/General/Challenge_Travel_Runner.Challenge_Travel_Runner' => 50.00,
        '/Game/Challenges/Progression/General/Challenge_Hacking_Hunter.Challenge_Hacking_Hunter' => 85.71,
        '/Game/Challenges/Progression/General/Challenge_Drones_Hunter.Challenge_Drones_Hunter' => 95.24,
        '/Game/Challenges/Progression/General/Challenge_TeamActions_Runner.Challenge_TeamActions_Runner' => 47.09,
        '/Game/Challenges/Progression/General/Challenge_HunterClose_Runner.Challenge_HunterClose_Runner' => 76.19,
        '/Game/Challenges/Progression/General/Challenge_Damage_Hunter.Challenge_Damage_Hunter' => 40.48,
        '/Game/Challenges/Progression/General/Challenge_ConstructDefeats_Runner.Challenge_ConstructDefeats_Runner' => 73.54,
        '/Game/Challenges/Progression/General/Challenge_Heal_Runner.Challenge_Heal_Runner' => 52.38,
        '/Game/Challenges/Progression/General/Challenge_RingOut_Hunter.Challenge_RingOut_Hunter' => 58.33,
        '/Game/Challenges/Progression/General/Challenge_Supercharge_Hunter.Challenge_Supercharge_Hunter' => 78.57,
        '/Game/Challenges/Progression/General/Challenge_TakeDamage_Runner.Challenge_TakeDamage_Runner' => 57.14,
        '/Game/Challenges/Progression/General/Challenge_Evade_Runner.Challenge_Evade_Runner' => 70.24,
        '/Game/Challenges/Progression/General/Challenge_Climb_Runner.Challenge_Climb_Runner' => 86.11,
        '/Game/Challenges/Challenge_Deliver_Runner.Challenge_Deliver_Runner' => 57.14,
        '/Game/Challenges/Progression/General/Challenge_Mark_Runner.Challenge_Mark_Runner' => 71.43,
        '/Game/Challenges/Progression/General/Challenge_Reveal_Hunter.Challenge_Reveal_Hunter' => 94.29,
        '/Game/Challenges/Progression/General/Challenge_HackCrates_Hunter.Challenge_HackCrates_Hunter' => 42.86,
        '/Game/Challenges/Progression/General/Challenge_SpendNPI_Runner.Challenge_SpendNPI_Runner' => 42.86,
        '/Game/Challenges/Progression/General/Challenge_CollectAmmo.Challenge_CollectAmmo' => 52.38,
        '/Game/Challenges/Progression/General/Challenge_CollectWeaponUpgrades_Runner.Challenge_CollectWeaponUpgrades_Runner' => 52.38,
        '/Game/Challenges/Progression/General/Challenge_CollectHealthCrates.Challenge_CollectHealthCrates' => 90.48,
        '/Game/Challenges/Progression/General/Challenge_DisableDrones_Runner.Challenge_DisableDrones_Runner' => 61.90,
        '/Game/Challenges/Challenge_DroneCharger_Hunter.Challenge_DroneCharger_Hunter' => 66.67,
        '/Game/Challenges/Progression/General/Challenge_Stomp_Hunter.Challenge_Stomp_Hunter' => 50.00,
        '/Game/Challenges/Progression/General/Challenge_Aim_Hunter.Challenge_Aim_Hunter' => 42.86,
        '/Game/Challenges/Progression/General/Challenge_DangerClose_Runner.Challenge_DangerClose_Runner' => 76.19,
        '/Game/Challenges/Progression/General/Challenge_SurviveAChase_Runner.Challenge_SurviveAChase_Runner' => 64.29,
        '/Game/Challenges/Progression/General/Challenge_AssistAChase_Runner.Challenge_AssistAChase_Runner' => 28.57,
        '/Game/Challenges/Progression/General/Challenge_Exit_Runner.Challenge_Exit_Runner' => 42.86,
        '/Game/Challenges/Progression/General/Challenge_BloodMode_Runner.Challenge_BloodMode_Runner' => 42.86,
        '/Game/Challenges/Progression/General/Challenge_LastMAnStanding_Hunter.Challenge_LastManStanding_Hunter' => 42.86,
    ];

    public function initOrGetGroups(InitOrGetGroupsRequest $request)
    {
        $response = new InitOrGetGroupsResponse();

        // If the request has set a player Id, use it.
        // if not use the current authenticated user from this session.
        // This is important because the hosts requests the progression and metadata groups for the other players
        // for tracking challenges and so on.
        if($request->playerId === null)
            $user = Auth::user();
        else
            $user = User::find($request->playerId);

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
                if ($group->getCharacter() !== false)
                    $response->addCharacterMetadataGroup($group->getCharacter(), $user);
            }
        }

        $response->addGeneralMetadata($user);

        return json_encode($response);
    }



    public function updateMetadataGroup(UpdateMetadataGroupRequest $request)
    {
        switch ($request->group) {
            // I dont know if the game ever tries to update the profile metadata since it's just the number of all
            // experience in your account. Well se it in the logs if it ever tries.
            case MetadataGroup::Profile:
                throw new \Exception('To be implemented');
            case MetadataGroup::Player:
                return $this->handleUpdatePlayerMetadata($request);
            case MetadataGroup::Hunter:
            case MetadataGroup::Runner:
                return $this->handleFactionMetadata($request);
            default:
                return $this->handleUpdateCharacterMetadata($request);
        }
    }

    private function handleUpdateCharacterMetadata(UpdateMetadataGroupRequest &$request): string|bool
    {
        // If the request has set a player Id, use it.
        // if not use the current authenticated user from this session.
        // This is important because the hosts requests the progression and metadata groups for the other players
        // for tracking challenges and so on.
        if($request->playerId === null)
            $user = Auth::user();
        else
            $user = User::find($request->playerId);


        $characterData = $user->playerData()->characterDataForCharacter($request->group->getCharacter());

        $convertedIds = UuidHelper::convertFromHexToUuidCollecton($request->metadata['equipment'], true);
        $characterData->equipment()->sync($convertedIds);

        $convertedIds = UuidHelper::convertFromHexToUuidCollecton($request->metadata['equippedPerks'], true);
        $characterData->equippedPerks()->sync($convertedIds);

        $convertedIds = UuidHelper::convertFromHexToUuidCollecton($request->metadata['equippedWeapons'], true);
        $characterData->equippedWeapons()->sync($convertedIds);

        $characterConfig = $characterData->character->getCharacter()->getItemConfigClass();
        $defaultCompletedIds = [
            ...$characterConfig::getDefaultEquippedBonuses(),
            ...$characterConfig::getDefaultEquippedWeapons(),
            ...$characterConfig::getDefaultEquipment(),
            ...$characterConfig::getDefaultPowers(),
            ...$characterConfig::getDefaultWeapons(),
            ...$characterConfig::getDefaultEquippedPerks(),
        ];

        $defaultCompletedIds = UuidHelper::convertFromHexToUuidCollecton($defaultCompletedIds, true);

        foreach ($request->metadata['pickedChallenges'] as $picked) {
            $itemId = Uuid::fromHexadecimal(new Hexadecimal($picked['itemId']));
            $pickedChallenge = $characterData->getPicketChallengeForItem($itemId);

            // Picked challenge for item already exists, so we just ignore the send one and keep ours because we don't want to reset the progress.
            if($pickedChallenge !== null)
                continue;

            foreach ($picked['list'] as $challenge) {
                $challengeId = Uuid::fromHexadecimal(new Hexadecimal($challenge['challengeId']));
                $assetPath = $challenge['challengeAsset'];
                $completionValue = static::reducePickedChallengeCompletionValue($assetPath, $challenge['challengeCompletionValue']);

                $attributes = [
                    'id' => $challengeId->toString(),
                    'completion_value' => $completionValue,
                    'asset_path' => $assetPath,
                ];

                if($defaultCompletedIds->has($itemId->toString()))
                    $attributes['progress'] = $completionValue;

                $newPicked = new PickedChallenge($attributes);

                $newPicked->characterData()->associate($characterData);
                $newPicked->catalogItem()->associate($itemId->toString());

                $newPicked->save();
            }
        }

        ++$characterData->readout_version;
        $characterData->save();

        $response = new UpdateMetadataResponse(
            $user->id,
            MetadataGroup::Player,
            $characterData->readout_version
        );

        return json_encode($response);
    }

    private function handleUpdatePlayerMetadata(UpdateMetadataGroupRequest &$request): string|bool
    {
        // If the request has set a player Id, use it.
        // if not use the current authenticated user from this session.
        // This is important because the hosts requests the progression and metadata groups for the other players
        // for tracking challenges and so on.
        if($request->playerId === null)
            $user = Auth::user();
        else
            $user = User::find($request->playerId);

        $playerData = $user->playerData();
        $playerData->last_faction = Faction::tryFrom($request->metadata['lastPlayedFaction']);
        $playerData->last_runner = Runner::tryFromTag($request->metadata['lastPlayedRunnerId']['tagName']);
        $playerData->last_hunter = Hunter::tryFromTag($request->metadata['lastPlayedHunterId']['tagName']);
        $playerData->has_played_tutorial = $request->metadata['shouldPlayWithoutContextualHelp'];
        $playerData->has_played_dg_1 = $request->metadata['hasPlayedDeathGarden1'];
        ++$playerData->readout_version;

        $playerData->save();

        $response = new UpdateMetadataResponse(
            $user->id,
            MetadataGroup::Player,
            $playerData->readout_version
        );

        return json_encode($response);
    }

    private function handleFactionMetadata(UpdateMetadataGroupRequest &$request): string|bool
    {
        throw new \Exception('Handle Update Faction Metadata not implemented yet');
        return false;
    }

    public static function reducePickedChallengeCompletionValue(
        string $challengeBlueprint,
        string $originalAmount,
    ): int {
        if(isset(static::PICKED_CHALLENGE_REDUCTION_MAPPING[$challengeBlueprint]))
            return $originalAmount - (static::PICKED_CHALLENGE_REDUCTION_MAPPING[$challengeBlueprint] / 100 * $originalAmount);
        return $originalAmount;
    }
}
