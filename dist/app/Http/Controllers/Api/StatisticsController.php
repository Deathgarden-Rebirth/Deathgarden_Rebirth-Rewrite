<?php

namespace App\Http\Controllers\Api;

use App\Enums\Game\Matchmaking\MatchmakingSide;
use App\Http\Controllers\Controller;
use App\Http\Responses\Api\Statistics\OnlinePlayersResponse;
use App\Models\Game\Matchmaking\QueuedPlayer;
use DB;
use Illuminate\Support\Facades\Cache;

class StatisticsController extends Controller
{
    const CACHE_KEY = 'online-players-response';

    public function getOnlinePlayers() {
        return Cache::remember(static::CACHE_KEY, 10, function () {
            $queuedHunters = QueuedPlayer::whereSide(MatchmakingSide::Hunter)->count();
            $queuedRunners = QueuedPlayer::whereSide(MatchmakingSide::Runner)->count();
            $inGamePlayers = DB::table('game_user')->count();

            return json_encode(new OnlinePlayersResponse(
                $queuedRunners,
                $queuedHunters,
                $inGamePlayers,
            ));
        });
    }
}
