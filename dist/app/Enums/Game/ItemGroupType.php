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
    case hunterVeteran = 'HunterGroupD';
}
