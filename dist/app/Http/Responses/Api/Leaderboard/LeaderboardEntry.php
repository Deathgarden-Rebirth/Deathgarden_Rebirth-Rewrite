<?php

namespace App\Http\Responses\Api\Leaderboard;

use App\Models\User\User;

class LeaderboardEntry implements \JsonSerializable
{
    public User $user;

    public int $score;

    public int $rank;

    public function __construct(
        User $user,
        int $score,
        int $rank
    )
    {}

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->user->id,
            'score' => $this->score,
            'rank' => $this->rank,
            'playerName' => $this->getPlayerName(),
        ];
    }

    public function getPlayerName(): string {
        return $this->user->last_known_username . '_' . \Str::substr($this->user->id, 0, 8);
    }
}