<?php

namespace App\Http\Responses\Api\Player;

use App\Http\Responses\Api\General\Reward;

class GetQuitterStateResponse
{
    /** @var Reward[]  */
    public array $stayMatchStreakRewards = [];

    public function __construct(
        public int $strikeRefreshTime,
        public int $strikeLeft,
        public int $stayMatchStreak,
        public int $stayMatchStreakPrevious,
        public bool $hasQuitOnce,
        public int $quitMatchStreak,
        public int $quitMatchStreakPrevious,
    )
    {}
}