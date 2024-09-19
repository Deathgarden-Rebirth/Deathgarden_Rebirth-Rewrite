<?php

namespace App\Http\Controllers\Api;

use App\Enums\Game\Matchmaking\MatchmakingSide;
use App\Enums\Game\Matchmaking\MatchStatus;
use App\Http\Controllers\Controller;
use App\Http\Responses\Api\Statistics\OnlinePlayersResponse;
use App\Models\Admin\LauncherMessage;
use App\Models\Admin\Versioning\LauncherVersion;
use App\Models\Game\Matchmaking\Game;
use App\Models\Game\Matchmaking\QueuedPlayer;
use DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

class StatisticsController extends Controller
{
    const CACHE_KEY = 'online-players-response';

    public function getOnlinePlayers() {
        return Cache::remember(static::CACHE_KEY, 10, function () {
            $queuedHunters = QueuedPlayer::whereSide(MatchmakingSide::Hunter)->count();
            $queuedRunners = QueuedPlayer::whereSide(MatchmakingSide::Runner)->count();
            $inGamePlayers = DB::table('game_user')
                ->join('games', 'game_user.game_id', '=', 'games.id')
                ->whereIn('games.status', [
                    MatchStatus::Closed,
                    MatchStatus::Opened,
                    MatchStatus::Created,
                ])->count();

            return Response::json(new OnlinePlayersResponse(
                $queuedRunners,
                $queuedHunters,
                $inGamePlayers,
            ));
        });
    }

    public function getLauncherVersion(): ?string {
        return json_encode(LauncherVersion::get()?->launcherVersion);
    }

    public function getLauncherMessage(): ?string {
        return json_encode(LauncherMessage::getMessage());
    }
}
