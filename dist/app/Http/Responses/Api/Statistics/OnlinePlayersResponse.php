<?php

namespace App\Http\Responses\Api\Statistics;

use JsonSerializable;

class OnlinePlayersResponse
{
    public function __construct(
        public int $queuedRunners,
        public int $queuedHunters,
        public int $inGamePlayers,
    )
    {
    }
}