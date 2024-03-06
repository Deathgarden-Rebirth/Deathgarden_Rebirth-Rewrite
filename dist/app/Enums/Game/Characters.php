<?php

namespace App\Enums\Game;

use Ramsey\Uuid\UuidInterface;

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

    public function getFaction(): Faction
    {
        return $this->isRunner() ? Faction::Runner : Faction::Hunter;
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

    public static function tryFromUuid(UuidInterface $uuid)
    {
        return match ($uuid->toString()) {
            'cca2272d-408e-d953-87f0-17bed437ff9a' => Characters::Inquisitor,
            'c50fffbf-4686-6131-82f4-5890651797ce' => Characters::Stalker,
            'ef96a202-4988-4d43-7b87-a6a4bde81b7f' => Characters::Poacher,
            '606129dc-45ab-9d16-b69e-2fa5c99a9835' => Characters::Mass,
            '759e44dd-469c-2841-75c2-d6a1ab0b0fa7' => Characters::Dash,
            '56b7b6f6-4737-12d0-b7a2-f992bb2c16cd' => Characters::Smoke,
            '234ffd46-4c55-514b-6c1e-738645993caa' => Characters::Ghost,
            'c300e3a8-4e57-1d54-9e01-4b9051a18be8' => Characters::Ink,
            '755d4dfe-40da-1512-b01e-3d8cff3c8d4d' => Characters::Sawbones,
            '38a4ef81-4082-2e49-8b2f-d196b757f7ad' => Characters::Switch,
            default => null,
        };
    }
}
