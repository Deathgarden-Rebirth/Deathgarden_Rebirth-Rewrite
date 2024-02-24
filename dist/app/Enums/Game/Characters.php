<?php

namespace App\Enums\Game;

enum Characters: string
{
    // Hunters
    case Stalker = 'Stalker';
    case Poacher = 'Poacher';
    case Inquisitor = 'Inquisitor';
    case Mass = 'Mass';

    // Runners
    case Smoke = 'Smoke';
    case Ink = 'Ink';
    case Ghost = 'Ghost';
    case Sawbones = 'Sawbones';
    case Switch = 'Switch';
    case Dash = 'Dash';

    public function getTag(): string
    {
        $runner = Runner::tryFrom($this->value);

        return $runner?->getTag() ?? Hunter::tryFrom($this->value)->getTag();
    }

    public function getCharacter(): Runner|Hunter
    {
        return Runner::tryFrom($this->value) ?? Hunter::tryFrom($this->value);
    }

    public function isHunter(): bool
    {
        return Hunter::tryFrom($this->value) !== null;
    }

    public function isRunner(): bool
    {
        return Runner::tryFrom($this->value) !== null;
    }

    public function getgroup(): ItemGroupType
    {
        $group = Runner::tryFrom($this->value)?->getGroupType() ?? Hunter::tryFrom($this->value)->getGroupType();

        return $group;
    }
}
