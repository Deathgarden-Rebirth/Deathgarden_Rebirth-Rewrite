<?php

namespace App\Models\Admin;

use App\Enums\Game\ExperienceEventType;
use App\Models\AbstractFileBasedModel;

class CurrencyMultipliers extends AbstractFileBasedModel
{
    const FILE_NAME = 'currencyMultipliers';

    const CACHE_DURATION = 86400; // 1 Day

    public float $currencyA = 1;
    public float $currencyB = 1;
    public float $currencyC = 1;

    protected static function getDefault(): ?static
    {
        return new static();
    }
}