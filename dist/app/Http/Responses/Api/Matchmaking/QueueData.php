<?php

namespace App\Http\Responses\Api\Matchmaking;

class QueueData
{
    public function __construct(
        public int  $position,
        public int  $eta = -1,
        public bool $stable = true,
    )
    {
    }
}