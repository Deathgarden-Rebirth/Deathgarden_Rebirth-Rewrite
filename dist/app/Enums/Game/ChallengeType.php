<?php

namespace App\Enums\Game;

enum ChallengeType: string
{
    case None = 'None';
    case Daily = 'Daily';
    case Weekly = 'Weekly';
    case Event = 'Event';
}
