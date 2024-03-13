<?php

namespace App\Http\Responses\Api\Matchmaking;

use App\Enums\Game\Matchmaking\MatchStatus;
use App\Enums\Game\Matchmaking\QueueStatus;
use App\Models\Game\Matchmaking\MatchConfiguration;

class QueueResponse
{
    public QueueStatus $status;

    public QueueData $queueData;

    public MatchData $matchData;
}

