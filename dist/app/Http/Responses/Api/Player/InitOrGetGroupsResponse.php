<?php

namespace App\Http\Responses\Api\Player;

use App\Enums\Game\Characters;
use App\Enums\Game\ItemGroupType;
use App\Models\Game\CharacterData;
use App\Models\User\PlayerData;
use App\Models\User\User;
use JsonSerializable;

class InitOrGetGroupsResponse
{
    public array $ProgressionGroups;

    public array $MetadataGroups;

    public function __construct(array $progressionGroups = [], array $metadataGroups = [])
    {
        $this->ProgressionGroups = $progressionGroups;
        $this->MetadataGroups = $metadataGroups;
    }

    public function addCharacterMetadataGroup(Characters $character, User $user): void
    {

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
        $this->ProgressionGroups[] = $group;
    }

    public function addFactionProgression(ItemGroupType $groupType, User $user): void {
        $playerData = $user->playerData();

        $group = new ProgressionGroup();

        $group->version = $playerData->readout_version;
        $group->objectId = $groupType;

        $group->level = match($groupType) {
            ItemGroupType::RunnerProgression => $playerData->runner_faction_level,
            ItemGroupType::HunterProgression => $playerData->hunter_faction_level,
            default => 1,
        };

        $group->currentExperience = match($groupType) {
            ItemGroupType::RunnerProgression => $playerData->runner_faction_experience,
            ItemGroupType::HunterProgression => $playerData->hunter_faction_experience,
            default => 0,
        };
        $group->experienceToReach = PlayerData::getRemainingFactionExperience($group->level);
        $this->ProgressionGroups[] = $group;
    }
}

class MetadataGroup
{
    public float $Version;

    public string $ObjectId;

    public float $SchemaVersion;

    public object $Data;

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
        $response = collect([
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

        return $response;
    }
}