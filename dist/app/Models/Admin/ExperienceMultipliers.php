<?php

namespace App\Models\Admin;

use App\Enums\Game\ExperienceEventType;
use App\Models\AbstractFileBasedModel;

class ExperienceMultipliers extends AbstractFileBasedModel
{
    const FILE_NAME = 'experienceMultipliers';

    const CACHE_DURATION = 86400; // 1 Day

    private float $constructDefeats = 1;

    private float $downing = 1;

    private float $drones = 1;

    private float $execution = 1;

    private float $gardenFinale = 1;

    private float $hacking = 1;

    private float $hunterClose = 1;
    private float $resources = 1;
    private float $teamActions = 1;

    public function setEventTypeMultiplier(ExperienceEventType $type, float $multiplier): void
    {
        if ($type === ExperienceEventType::ConstructDefeats)
            $this->constructDefeats = $multiplier;
        if ($type === ExperienceEventType::Downing)
            $this->downing = $multiplier;
        if ($type === ExperienceEventType::Drones)
            $this->drones = $multiplier;
        if ($type === ExperienceEventType::Execution)
            $this->execution = $multiplier;
        if ($type === ExperienceEventType::GardenFinale)
            $this->gardenFinale = $multiplier;
        if ($type === ExperienceEventType::Hacking)
            $this->hacking = $multiplier;
        if ($type === ExperienceEventType::HunterClose)
            $this->hunterClose = $multiplier;
        if ($type === ExperienceEventType::Resources)
            $this->resources = $multiplier;
        if ($type === ExperienceEventType::TeamActions)
            $this->teamActions = $multiplier;
    }

    public function getEventTypeMultiplier(ExperienceEventType $type): float {
        return match ($type) {
            ExperienceEventType::ConstructDefeats => $this->constructDefeats,
            ExperienceEventType::Downing => $this->downing,
            ExperienceEventType::Drones => $this->drones,
            ExperienceEventType::Execution => $this->execution,
            ExperienceEventType::GardenFinale => $this->gardenFinale,
            ExperienceEventType::Hacking => $this->hacking,
            ExperienceEventType::HunterClose => $this->hunterClose,
            ExperienceEventType::Resources => $this->resources,
            ExperienceEventType::TeamActions => $this->teamActions,
            default => 0,
        };
    }

    protected static function getDefault(): ?static
    {
        return new static();
    }
}