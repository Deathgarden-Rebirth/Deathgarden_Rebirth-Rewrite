<?php

namespace App\Http\Responses\Api\Leaderboard;

use Illuminate\Support\Carbon;

class GetScoresResponse implements \JsonSerializable
{
    /**
     * @var array
     */
    public array $topScores = [];

    public array $playerScores = [];

    public int $leaderboardSize;

    public Carbon $leaderboardReset;

    public function jsonSerialize(): mixed
    {
        return [
            'topScores' => $this->topScores,
            'playerScores' => $this->playerScores,
            'leaderboardSize' => $this->leaderboardSize,
            'leaderboardReset' => $this->leaderboardReset->getTimestamp(),
        ];
    }
}