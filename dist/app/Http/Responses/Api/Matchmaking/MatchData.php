<?php

namespace App\Http\Responses\Api\Matchmaking;

use App\Enums\Game\Matchmaking\MatchStatus;

class MatchData
{
    public string $matchId;

    public string $category = 'Steam-te-18f25613-36778-ue4-374f864b';

    public string $rank = '1';

    public int $creationDateTime;

    public bool $excludeFriends = false;

    public bool $excludeClanMembers = false;

    public MatchStatus $status;

    public ?string $creator;

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