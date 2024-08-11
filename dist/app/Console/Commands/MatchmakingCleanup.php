<?php

namespace App\Console\Commands;

use App\Enums\Game\Matchmaking\MatchStatus;
use App\Models\Game\Matchmaking\Game;
use App\Models\Game\Matchmaking\QueuedPlayer;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MatchmakingCleanup extends Command
{
    // After how many minutes a queued palyer gets removed from the queue or from an open match in Minutes.
    const PLAYER_HEARTBEAT_TIMEOUT = 1;

    // After how many minutes a closed game gets deleted automatically when it hasn't been killed normally yet.
    const GAME_MAX_TIME = 15;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'matchmaking:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup stale or bugged matchmaking games and queued users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Delete queued players that haven't sent a queue resquest in the given period of time,
        // which means they crashed or closed the game without sending cancle.
        QueuedPlayer::where('updated_at', '<', Carbon::now()->subMinutes(static::PLAYER_HEARTBEAT_TIMEOUT))
            ->delete();

        Game::where('status', '=', MatchStatus::Closed->value)
            ->where('updated_at', '<', Carbon::now()->subMinutes(static::GAME_MAX_TIME))
            ->delete();

        // Select User Ids that joined a game and haven't sent the match request for a period of time
        // This porpably means they crashed or never joined the game in the first place.
        $usersToRemove = DB::table('game_user')->join('games', 'game_user.game_id', '=', 'games.id')
            ->whereIn('games.status', [
                MatchStatus::Created->value,
                MatchStatus::Opened->value,
            ])
            ->where('game_user.updated_at', '<', Carbon::now()->subMinutes(static::PLAYER_HEARTBEAT_TIMEOUT))
            ->get(['user_id']);

        $userIdArray = [];
        foreach ($usersToRemove as $user) {
            $userIdArray[] = $user->user_id;
        }

        // Delete a game if one of the to be removed players is the host.
        Game::whereIn('creator_user_id', $userIdArray)->delete();

        // Delete them afterwards.
        DB::table('game_user')->whereIn('user_id', $userIdArray)->delete();
    }
}
