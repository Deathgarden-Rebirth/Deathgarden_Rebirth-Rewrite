<?php

namespace App\Enums\Game\Matchmaking;

enum QueueStatus: string
{
    case PendingOnline = 'PendingOnline';
    case Online = 'Online';
    case PendingWarparty = 'PendingWarparty';
    case Queued = 'Queued';
    case Matched = 'Matched';
    case Failed = 'Failed';
    case ServerFull = 'ServerFull';
    case FriendNotPlaying = 'FriendNotPlaying';
    case WarpartyFull = 'WarpartyFull';
}
