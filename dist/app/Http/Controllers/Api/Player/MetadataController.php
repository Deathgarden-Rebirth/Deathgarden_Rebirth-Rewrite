<?php

namespace App\Http\Controllers\Api\Player;

use App\Enums\Api\UpdateMetadataReason;
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
use App\Models\Game\Challenge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Uuid;

class MetadataController extends Controller
{
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
        $user = Auth::user();
        $characterData = $user->playerData()->characterDataForCharacter($request->group->getCharacter());

        $convertedIds = UuidHelper::convertFromHexToUuidCollecton($request->metadata['equipment'], true);
        $characterData->equipment()->sync($convertedIds);

        $convertedIds = UuidHelper::convertFromHexToUuidCollecton($request->metadata['equippedPerks'], true);
        $characterData->equippedPerks()->sync($convertedIds);

        $convertedIds = UuidHelper::convertFromHexToUuidCollecton($request->metadata['equippedWeapons'], true);
        $characterData->equippedWeapons()->sync($convertedIds);

        $convertedIds = UuidHelper::convertFromHexToUuidCollecton($request->metadata['equippedBonuses'], true);
        $characterData->equippedBonuses()->sync($convertedIds);

        $characterData->pickedChallenges()->detach();
        foreach ($request->metadata['pickedChallenges'] as $picked) {
            $itemId = Uuid::fromHexadecimal(new Hexadecimal($picked['itemId']));

            foreach ($picked['list'] as $challenge) {
                $challengeId = Uuid::fromHexadecimal(new Hexadecimal($challenge['challengeId']));
                $completionValue = $challenge['challengeCompletionValue'];
                $assetPath = $challenge['challengeAsset'];

                $foundChallenge = Challenge::firstOrCreate(
                    [
                        'id' => $challengeId->toString(),
                    ],
                    [
                        'id' => $challengeId->toString(),
                        'completion_value' => $completionValue,
                        'asset_path' => $assetPath,
                    ]
                );


                $characterData->pickedChallenges()->attach($foundChallenge->id, [
                    'catalog_item_id' => $itemId->toString(),
                ]);
            }
        }

        $response = new UpdateMetadataResponse(
            $user->id,
            MetadataGroup::Player,
            $characterData->readout_version
        );

        return json_encode($response);
    }

    private function handleUpdatePlayerMetadata(UpdateMetadataGroupRequest &$request): string|bool
    {
        $user = Auth::user();

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
}
