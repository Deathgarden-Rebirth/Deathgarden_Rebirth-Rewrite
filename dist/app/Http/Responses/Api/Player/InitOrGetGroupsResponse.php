<?php

namespace App\Http\Responses\Api\Player;

use App\Classes\Character\CharacterItemConfig;
use App\Enums\Game\Characters;
use App\Enums\Game\ItemGroupType;
use App\Helper\Uuid\UuidHelper;
use App\Models\Game\Challenge;
use App\Models\Game\CharacterData;
use App\Models\User\PlayerData;
use App\Models\User\User;
use JsonSerializable;
use Ramsey\Uuid\Uuid;

class InitOrGetGroupsResponse
{
    public array $progressionGroups;

    public array $metadataGroups;

    public function __construct(array $progressionGroups = [], array $metadataGroups = [])
    {
        $this->progressionGroups = $progressionGroups;
        $this->metadataGroups = $metadataGroups;
    }

    public function addGeneralMetadata(User $user): void
    {
        $playerData = $user->playerData();

        $this->metadataGroups[] = new GeneralMetadata(
            $playerData->readout_version,
            'HunterMetadata'
        );

        $this->metadataGroups[] = new GeneralMetadata(
            $playerData->readout_version,
            'RunnerMetadata'
        );

        $profileMetadata = new GeneralMetadata(
            $playerData->readout_version,
            'ProfileMetadata'
        );

        $profileMetadata->data['characterCumulativeExperience'] = $playerData->getCumulativeExperience();

        $this->metadataGroups[] = $profileMetadata;

        $playerMetadata = new GeneralMetadata(
            $playerData->readout_version,
            'PlayerMetadata'
        );

        $playerMetadata->data['lastPlayedFaction'] = $playerData->last_faction->value;
        $playerMetadata->data['lastPlayedRunnerId'] = [
            'tagName' => $playerData->last_runner->getTag()
        ];
        $playerMetadata->data['lastPlayedHunterId'] = [
            'tagName' => $playerData->last_hunter->getTag()
        ];
        $playerMetadata->data['shouldPlayWithoutContextualHelp'] = $playerData->has_played_tutorial;
        $playerMetadata->data['hasPlayedDeathGarden1'] = $playerData->has_played_dg_1;

        $this->metadataGroups[] = $playerMetadata;
    }

    public function addCharacterMetadataGroup(Characters $character, User $user): void
    {
        $newGroup = new MetadataGroup();
        /** @var CharacterItemConfig|string $itemConfigClass */
        $itemConfigClass = $character->getCharacter()->getItemConfigClass();

        $characterData = $user->playerData()->characterDataForCharacter($character);
        $characterData->validateEquippedItems();

        $newGroup->equippedPerks = UuidHelper::convertFromUuidToHexCollection($characterData->equippedPerks()->allRelatedIds())->toArray();
        $newGroup->equippedWeapons = UuidHelper::convertFromUuidToHexCollection($characterData->equippedWeapons()->allRelatedIds())->toArray();
        $newGroup->equipment = UuidHelper::convertFromUuidToHexCollection($characterData->equipment()->allRelatedIds())->toArray();
        $newGroup->equippedBonuses = UuidHelper::convertFromUuidToHexCollection($characterData->equippedBonuses()->allRelatedIds())->toArray();
        $newGroup->equippedPowers = $itemConfigClass::getDefaultPowers();
        $newGroup->prestigeLevel = $characterData->prestige_level;

        $pickedChallenges = $characterData->pickedChallenges;
        $resultChallenges = collect();

        foreach ($pickedChallenges as $picked) {
            // Because a pciked challenge for a specific item can have multiple challenges,
            // we look if there is already an entry with the item id.
            $foundKey = $resultChallenges->search(function ($value, $key) use ($picked) {
                return $value['itemId'] === Uuid::fromString($picked->catalog_item_id)->getHex()->toString();
            });

            // if there is not, we just create it entirely and fill the list with the current challenge
            if ($foundKey === false) {
                $newEntry = collect([
                    'itemId' => Uuid::fromString($picked->catalog_item_id)->getHex()->toString(),
                    'list' => collect([
                        [
                            'challengeId' => Uuid::fromString($picked->id)->getHex()->toString(),
                            'challengeCompletionValue' => $picked->completion_value,
                            'challengeAsset' => $picked->asset_path,
                        ]
                    ])]);

                $resultChallenges->add($newEntry);
            } // and if there is we just add the challenge to the list of the item.
            else {
                $resultChallenges[$foundKey]['list']->add([
                    'challengeId' => Uuid::fromString($picked->id)->getHex()->toString(),
                    'challengeCompletionValue' => $picked->completion_value,
                    'challengeAsset' => $picked->asset_path,
                ]);
            }
        }

        $newGroup->pickedChallenges = $resultChallenges->toArray();

        $newGroup->version = $characterData->readout_version;
        $newGroup->objectId = $character->getgroup();

        $this->metadataGroups[] = $newGroup;
    }

    public function addCharacterProgressionGroup(Characters $character, User $user): void
    {
        /** @var CharacterData $characterData */
        $characterData = $user->playerData()->characterData()->firstWhere('character', $character->value);
        $group = new ProgressionGroup();

        $group->version = $characterData->readout_version;
        $group->objectId = $characterData->character->getgroup();
        $group->level = $characterData->level;
        $group->currentExperience = $characterData->experience;
        $group->experienceToReach = CharacterData::getExperienceForLevel($group->level);
        $this->progressionGroups[] = $group;
    }

    public function addFactionProgression(ItemGroupType $groupType, User $user): void
    {
        $playerData = $user->playerData();

        $group = new ProgressionGroup();

        $group->version = $playerData->readout_version;
        $group->objectId = $groupType;

        $group->level = match ($groupType) {
            ItemGroupType::RunnerProgression => $playerData->runner_faction_level,
            ItemGroupType::HunterProgression => $playerData->hunter_faction_level,
            default => 1,
        };

        $group->currentExperience = match ($groupType) {
            ItemGroupType::RunnerProgression => $playerData->runner_faction_experience,
            ItemGroupType::HunterProgression => $playerData->hunter_faction_experience,
            default => 0,
        };
        $group->experienceToReach = PlayerData::getRemainingFactionExperience($group->level);
        $this->progressionGroups[] = $group;
    }
}

class MetadataGroup implements JsonSerializable
{
    public int $version;

    public ItemGroupType $objectId;

    public array $equippedPerks;

    public array $equippedWeapons;

    public array $equipment;

    public array $equippedBonuses;

    public array $pickedChallenges;

    public array $equippedPowers;

    public int $prestigeLevel;

    public function jsonSerialize(): mixed
    {
        $array = [
            'version' => $this->version,
            'objectId' => $this->objectId,
            'schemaVersion' => 1,
            'data' => [
                'equippedPerks' => $this->equippedPerks,
                'equippedWeapons' => $this->equippedWeapons,
                'equipment' => $this->equipment,
                'equippedBonuses' => $this->equippedBonuses,
                'pickedChallenges' => $this->pickedChallenges,
                'equippedPowers' => $this->equippedPowers,
                'characterId' => ['tagName' => $this->objectId->getCharacter()->getTag()],
                'prestigeLevel' => $this->prestigeLevel,
            ],
        ];

        // Unused Consumable items that were never developed in Deathgarden, but still present in request.
        // Can be hard-coded because they never change.
        if ($this->objectId->getCharacter()->isHunter())
            $array['data']['equippedConsumables'] = ['1069E6DF40AB4CAEF2AF03B4FD60BB22'];
        else
            $array['data']['equippedConsumables'] = ['487DEBE247818A01797AF5B3FD04C2B2'];

        return collect($array);
    }
}

class ProgressionGroup implements JsonSerializable
{
    public int $version;

    public ItemGroupType $objectId;

    public int $level;

    public int $currentExperience;

    public int $experienceToReach;

    public function jsonSerialize(): mixed
    {
        return collect([
            'version' => $this->version,
            'objectId' => $this->objectId,
            'schemaVersion' => 1,
            'data' => [
                'experience' => [
                    'level' => $this->level,
                    'experienceToReach' => $this->experienceToReach,
                    'currentExperience' => $this->currentExperience,
                ],
                'metadata' => [],
            ]
        ]);
    }
}

class GeneralMetadata
{
    public array $data = [];

    public int $schemaVersion = 1;

    public function __construct(
        public int    $version,
        public string $objectId,
    )
    {

    }
}