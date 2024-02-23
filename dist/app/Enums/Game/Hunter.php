<?php

namespace App\Enums\Game;

use App\Classes\Character\HunterItemConfig\InquisitorItemConfig;
use App\Classes\Character\HunterItemConfig\PoacherItemConfig;
use App\Classes\Character\HunterItemConfig\StalkerItemConfig;
use App\Classes\Character\HunterItemConfig\VeteranItemConfig;

enum Hunter: string
{
    case Stalker = 'Stalker';
    case Poacher = 'Poacher';
    case Inquisitor = 'Inquisitor';
    case Mass = 'Mass';

    public function getTag()
    {
        return 'Hunter.'.$this->value;
    }

    public function getGroupType(): ItemGroupType
    {
        return match ($this) {
            Hunter::Stalker => ItemGroupType::HunterStalker,
            Hunter::Poacher => ItemGroupType::HunterPoacher,
            Hunter::Inquisitor => ItemGroupType::HunterInquisitor,
            Hunter::Mass => ItemGroupType::HunterVeteran,
        };
    }

    public function getItemConfigClass(): string
    {
        return match ($this) {
            Hunter::Stalker => StalkerItemConfig::class,
            Hunter::Poacher => PoacherItemConfig::class,
            Hunter::Inquisitor => InquisitorItemConfig::class,
            Hunter::Mass => VeteranItemConfig::class,
        };
    }
}
