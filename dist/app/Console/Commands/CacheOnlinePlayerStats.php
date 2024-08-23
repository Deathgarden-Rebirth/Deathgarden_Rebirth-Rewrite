<?php

namespace App\Console\Commands;

use App\Enums\Game\Matchmaking\MatchmakingSide;
use App\Http\Responses\Api\Statistics\OnlinePlayersResponse;
use App\Models\Game\Matchmaking\QueuedPlayer;
use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class CacheOnlinePlayerStats extends Command
{
    const CACHE_KEY = 'online-players-response';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cache-online-player-stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Caches the queued and in game players';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $queuedHunters = QueuedPlayer::whereSide(MatchmakingSide::Hunter)->count();
        $queuedRunners = QueuedPlayer::whereSide(MatchmakingSide::Runner)->count();
        $inGamePlayers = DB::table('game_user')->count();

        Cache::set(self::CACHE_KEY, json_encode(new OnlinePlayersResponse(
            $queuedRunners,
            $queuedHunters,
            $inGamePlayers,
        )));
    }
}
