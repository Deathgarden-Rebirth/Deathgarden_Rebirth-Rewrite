w<?php

namespace App\Enums\Game;

enum MetadataGroup: string
{
    case Sawbones = 'RunnerGroupA';
    case Inked = 'RunnerGroupB';
    case Ghost = 'RunnerGroupC';
    case Switch = 'RunnerGroupD';
    case Fog = 'RunnerGroupE';

    case Dash = 'RunnerGroupF';
    case Stalker = 'HunterGroupA';
    case Inquisitor = 'HunterGroupB';
    case Poacher = 'HunterGroupC';
    case Veteran = 'HunterGroupD';
    case Bounty = 'HunterGroupE';

    case Profile = 'ProfileMetadata';

    case Player = 'PlayerMetadata';

    case Runner = 'RunnerMetadata';

    case Hunter = 'HunterMetadata';

    public function getCharacter(): Characters|null
    {
        return ItemGroupType::tryFrom($this->value)->getCharacter();
    }
}
