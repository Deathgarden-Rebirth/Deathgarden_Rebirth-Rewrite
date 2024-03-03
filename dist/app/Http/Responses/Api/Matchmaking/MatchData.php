<?php

namespace App\Http\Responses\Api\Matchmaking;

use App\Enums\Game\Matchmaking\MatchmakingSide;
use App\Enums\Game\Matchmaking\MatchStatus;
use App\Models\Game\Matchmaking\Game;

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

    public static function fromGame(Game &$game): MatchData {
        $data = new MatchData();

        $data->matchId = $game->id;
        $data->creationDateTime = $game->created_at->getTimestamp();
        $data->status = $game->status;
        $data->creator = $game->creator?->id;

        $players = $game->players;
        $data->players = $players->pluck('id')->toArray();
        $data->sideA = $players->where('pivot.side', MatchmakingSide::Hunter->value)->pluck('id')->toArray();
        $data->sideB = $players->where('pivot.side', MatchmakingSide::Runner->value)->pluck('id')->toArray();

        if($game->session_settings !== '')
            $data->customData = (object)[
                'SessionSettings' => $game->session_settings,
            ];

        $data->props = new MatchProperties(
            $game->matchConfiguration,
        );

        return $data;
    }
}