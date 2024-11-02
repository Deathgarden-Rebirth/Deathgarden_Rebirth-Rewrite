<?php

namespace App\Enums\Game;

use App\Classes\Character\CharacterItemConfig;
use App\Classes\Character\HunterItemConfig\InquisitorItemConfig;
use App\Classes\Character\HunterItemConfig\PoacherItemConfig;
use App\Classes\Character\HunterItemConfig\StalkerItemConfig;
use App\Classes\Character\HunterItemConfig\VeteranItemConfig;
use App\Classes\Character\HunterItemConfig\BountyItemConfig;

enum Hunter: string
{
    case Stalker = 'Stalker';
    case Poacher = 'Poacher';
    case Inquisitor = 'Inquisitor';
    case Mass = 'Mass';
    case Bounty = 'Bounty';

    public function getTag()
    {
        return 'Hunter.'.$this->value;
    }

    public static function tryFromTag(string $tag): Hunter|null
    {
        return match ($tag) {
            'Hunter.Stalker' => Hunter::Stalker,
            'Hunter.Poacher' => Hunter::Poacher,
            'Hunter.Inquisitor' => Hunter::Inquisitor,
            'Hunter.Mass' => Hunter::Mass,
            'Hunter.Bounty' => Hunter::Bounty,
            default => null,
        };
    }

    public function getGroupType(): ItemGroupType
    {
        return match ($this) {
            Hunter::Stalker => ItemGroupType::HunterStalker,
            Hunter::Poacher => ItemGroupType::HunterPoacher,
            Hunter::Inquisitor => ItemGroupType::HunterInquisitor,
            Hunter::Mass => ItemGroupType::HunterVeteran,
            Hunter::Bounty => ItemGroupType::HunterBounty,
        };
    }

    public function getItemConfigClass(): string|CharacterItemConfig
    {
        return match ($this) {
            Hunter::Stalker => StalkerItemConfig::class,
            Hunter::Poacher => PoacherItemConfig::class,
            Hunter::Inquisitor => InquisitorItemConfig::class,
            Hunter::Mass => VeteranItemConfig::class,
            Hunter::Bounty => BountyItemConfig::class,
        };
    }
}
