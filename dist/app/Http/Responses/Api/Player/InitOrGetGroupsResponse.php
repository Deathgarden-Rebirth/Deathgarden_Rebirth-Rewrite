<?php

namespace App\Http\Responses\Api\Player;

use App\Classes\Unreal\GameplayTag;
use App\Enums\Game\Faction;
use App\Models\CharacterData as CharacterDataModel;

class InitOrGetGroupsResponse
{
    public array $ProgressionGroups;

    public array $MetadataGroups;

    public function __construct(array $progressionGroups = [], array $metadataGroups = [])
    {
        $this->ProgressionGroups = $progressionGroups;
        $this->MetadataGroups = $metadataGroups;
    }
}

class SplinteredState
{
    public string $ObjectId;

    public float $Version;

    public float $SchemaVersion;

    public object $Data;

    public function setDataFromCharacterData(CharacterDataModel $characterData): static
    {
        $this->Data = new CharacterData($characterData->character->getTag());

        return $this;
    }

}
class CharacterData {
    public GameplayTag $CharacterId;

    public array $Equipment = [];

    public array $EquippedPerks = [];

    public array $EquippedPowers = [];

    public array $EquippedWeapons = [];

    public array $EquippedBonuses = [];

    public function __construct(string $characterGameplayTag)
    {
        $this->CharacterId = new GameplayTag($characterGameplayTag);
    }
}

class PlayerData {
    public Faction $LastPlayedFaction;

    public GameplayTag $LastPlayedRunnerId;

    public GameplayTag $LastPlayedHunterId;

    public bool $shouldPlayWithoutContextualHelp;

    public bool $hasPlayedDeathGarden1;

    public function __construct(\App\Models\PlayerData $playerData)
    {
        $this->LastPlayedFaction = $playerData->last_faction;
        $this->LastPlayedRunnerId = new GameplayTag($playerData->last_runner->getTag());
        $this->LastPlayedHunterId = new GameplayTag($playerData->last_hunter->getTag());
        $this->shouldPlayWithoutContextualHelp = $playerData->has_played_tutorial;
        $this->hasPlayedDeathGarden1 = $playerData->has_played_dg_1;
    }
}