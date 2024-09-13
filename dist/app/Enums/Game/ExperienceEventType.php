<?php

namespace App\Enums\Game;

use App\Models\Admin\ExperienceMultipliers;

enum ExperienceEventType: string
{
    case CompletedChallenges = 'CompletedChallenges';
    case ConstructDefeats = 'ConstructDefeats';
    case Downing = 'Downing';
    case Drones = 'Drones';
    case Execution = 'Execution';
    case GardenFinale = 'GardenFinale';
    case Hacking = 'Hacking';
    case HunterClose = 'HunterClose';
    case Resources = 'Resources';
    case TeamActions = 'TeamActions';

    public function getMultiplier(): float {
        $experienceMultipliers = ExperienceMultipliers::get();

        return $experienceMultipliers->getEventTypeMultiplier($this);
    }
}
