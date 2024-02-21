<?php

namespace App\Enums\Game;

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
}
