<?php

namespace App\Enums\Game;

enum ItemGroupType: string
{
    case None = '';
    case RunnerProgression = 'RunnerProgression';
    case RunnerSawbones = 'RunnerGroupA';
    case RunnerInked = 'RunnerGroupB';
    case RunnerGhost = 'RunnerGroupC';
    case RunnerSwitch = 'RunnerGroupD';
    case RunnerFog = 'RunnerGroupE';
    case RunnerDash = 'RunnerGroupF';

    case HunterProgression = 'HunterProgression';
    case HunterStalker = 'HunterGroupA';
    case HunterInquisitor = 'HunterGroupB';
    case HunterPoacher = 'HunterGroupC';
    case HunterVeteran = 'HunterGroupD';

    // Not used by game but needed for InitOrGetGroups
    case PlayerProgression = 'PlayerProgression';

    public function getCharacter(): Characters|false
    {
        return match ($this) {
            ItemGroupType::RunnerSawbones => Characters::Sawbones,
            ItemGroupType::RunnerInked => Characters::Ink,
            ItemGroupType::RunnerGhost => Characters::Ghost,
            ItemGroupType::RunnerSwitch => Characters::Switch,
            ItemGroupType::RunnerFog => Characters::Smoke,
            ItemGroupType::RunnerDash => Characters::Dash,

            ItemGroupType::HunterStalker => Characters::Stalker,
            ItemGroupType::HunterInquisitor => Characters::Inquisitor,
            ItemGroupType::HunterPoacher => Characters::Poacher,
            ItemGroupType::HunterVeteran => Characters::Mass,
            default => false,
        };
    }
}
