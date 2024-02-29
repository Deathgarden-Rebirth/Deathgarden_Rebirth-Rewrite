<?php

namespace App\Enums\Game;

use App\Classes\Character\CharacterItemConfig;
use App\Classes\Character\RunnerItemConfig\DashItemConfig;
use App\Classes\Character\RunnerItemConfig\FogItemConfig;
use App\Classes\Character\RunnerItemConfig\GhostItemConfig;
use App\Classes\Character\RunnerItemConfig\InkedItemConfig;
use App\Classes\Character\RunnerItemConfig\SawbonesItemConfig;
use App\Classes\Character\RunnerItemConfig\SwitchItemConfig;

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

    public static function tryFromTag(string $tag): Runner|null
    {
        return match ($tag) {
            'Runner.Smoke' => Runner::Smoke,
            'Runner.Ink' => Runner::Ink,
            'Runner.Ghost' => Runner::Ghost,
            'Runner.Sawbones' => Runner::Sawbones,
            'Runner.Switch' => Runner::Switch,
            'Runner.Dash' => Runner::Dash,
            default => null,
        };
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

    public function getItemConfigClass(): string|CharacterItemConfig
    {
        return match ($this) {
            self::Smoke => FogItemConfig::class,
            self::Ink => InkedItemConfig::class,
            self::Ghost => GhostItemConfig::class,
            self::Sawbones => SawbonesItemConfig::class,
            self::Switch => SwitchItemConfig::class,
            self::Dash => DashItemConfig::class,
        };
    }
}
