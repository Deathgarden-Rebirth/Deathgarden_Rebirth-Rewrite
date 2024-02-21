<?php

namespace App\Enums\Game;

enum Runner: string
{
    case Smoke = 'Smoke';
    case Ink = 'Ink';
    case Ghost = 'Ghost';
    case Sawbones = 'Sawbones';
    case Switch = 'Switch';
    case Dash = 'Dash';

    public function getTag(): string
    {
        return 'Runner.'.$this->value;
    }

    public function getGroupType(): ItemGroupType
    {
        return match ($this) {
            Runner::Smoke => ItemGroupType::RunnerFog,
            Runner::Ink => ItemGroupType::RunnerInked,
            Runner::Ghost => ItemGroupType::RunnerGhost,
            Runner::Sawbones => ItemGroupType::RunnerSawbones,
            Runner::Switch => ItemGroupType::RunnerSwitch,
            Runner::Dash => ItemGroupType::RunnerDash,
        };
    }
}
