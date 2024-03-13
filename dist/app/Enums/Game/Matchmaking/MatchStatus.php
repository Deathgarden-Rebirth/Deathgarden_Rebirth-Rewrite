<?php

namespace App\Enums\Game\Matchmaking;

enum MatchStatus: string
{
    case None = 'None';
    case NoMatch = 'NoMatch';
    case Created = 'Created';
    case Creating = 'Creating';
    case DelayedCreation = 'DelayedCreation';
    case Opened = 'Opened';
    case Completed = 'Completed';
    case Timedout = 'Timedout';
    case Closing = 'Closing';
    case Closed = 'Closed';
    case Killing = 'Killing';
    case Killed = 'Killed';
    case Destroyed = 'Destroyed';
}
