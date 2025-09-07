<?php

namespace App\Enums\Game;

enum Faction: string
{
    case None = 'None';
    case Hunter = 'Hunter';
    case Runner = 'Runner';
    case Emcee = 'Emcee';

    /**
     * @return array<Runner|Hunter>
     */
    public function getCharacterList(): array {
        return match ($this) {
            self::Hunter => [
                Hunter::Inquisitor,
                Hunter::Stalker,
                Hunter::Poacher,
                Hunter::Mass
            ],
            self::Runner => [
                Runner::Smoke,
                Runner::Dash,
                Runner::Switch,
                Runner::Ghost,
                Runner::Sawbones,
                Runner::Ink,
            ],
            default => [],
        };
    }
}
