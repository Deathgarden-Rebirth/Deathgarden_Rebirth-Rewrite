<?php

namespace App\Http\Responses\Api\Matchmaking;

use App\Enums\Game\Matchmaking\MatchStatus;
use App\Enums\Game\Matchmaking\QueueStatus;

class QueueResponse
{
    public QueueStatus $status;

    public QueueData $queueData;

    public MatchData $matchData;
}

class QueueData {
    public function __construct(
        public int $position,
        public int $eta,
        public bool $stable = true,
    )
    {}
}

class MatchData {
    public string $matchId;

    public string $category;

    public string $rank = '1';

    public int $creationDateTime;

    public bool $excludeFriends = false;

    public bool $excludeClanMembers = false;

    public MatchStatus $status;

    public string $creator;

    /**
     * @var string[]
     */
    public array $players = [];

    /**
     * @var string[]
     */
    public array $sideA = [];

    /**
     * @var string[]
     */
    public array $sideB;

    public object $customData;

    public object $props;

    public int $schema = 3;

    public function __construct()
    {
        $this->customData = new \stdClass();
        $this->props = new \stdClass();
    }
}