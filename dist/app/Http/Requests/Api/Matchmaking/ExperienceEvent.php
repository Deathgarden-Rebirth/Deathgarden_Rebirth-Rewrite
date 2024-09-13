<?php

namespace App\Http\Requests\Api\Matchmaking;

use App\Enums\Game\ExperienceEventType;

class ExperienceEvent
{
    public function __construct(
        public ExperienceEventType $type,
        public int $amount,
    )
    { }
}