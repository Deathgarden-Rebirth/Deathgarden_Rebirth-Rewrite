<?php

namespace App\Enums\Game;

enum ItemOrigin: string
{
    case None = 'None';
    case Alienware = 'Alienware';
    case Deluxe = 'Deluxe';
    case DeadByDaylight = 'DeadByDaylight';
    case HeartsAndMinds = 'HeartAndMinds';
    case DeathGarden1 = 'DeathGarden1';
    case Event = 'Event';
    case DlcT800 = 'DlcT800';
}
