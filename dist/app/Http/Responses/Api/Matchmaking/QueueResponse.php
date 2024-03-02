<?php

namespace App\Http\Responses\Api\Matchmaking;

use App\Enums\Game\Matchmaking\MatchStatus;
use App\Enums\Game\Matchmaking\QueueStatus;
use App\Models\Game\Matchmaking\MatchConfiguration;
use Illuminate\Support\Facades\Hash;

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
    public array $sideB = [];

    public object $customData;

    public object $props;

    public int $schema = 3;

    public function __construct()
    {
        $this->customData = new \stdClass();
        $this->props = new \stdClass();
    }
}

class MatchProperties implements \JsonSerializable {
    public MatchConfiguration $matchConfiguration;

    protected string $platform = 'Windows';

    public function __construct(MatchConfiguration $matchConfiguration)
    {
        $this->matchConfiguration = $matchConfiguration;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'matchConfiguration' => $this->matchConfiguration->asset_path,
            'countA' => $this->matchConfiguration->hunters,
            'countB' => $this->matchConfiguration->runners,
            'gameMode' => md5($this->matchConfiguration->asset_path).'-Default',
            'platform' => $this->platform,
        ];
    }
}