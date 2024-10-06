<?php

namespace App\Console\Commands;

use App\Enums\Game\Faction;
use App\Enums\Game\Matchmaking\MatchStatus;
use App\Models\Game\Matchmaking\Game;
use App\Models\Game\Matchmaking\QueuedPlayer;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MatchmakingCleanup extends Command
{
    // After how many minutes a queued palyer gets removed from the queue or from an open match in Minutes.
    const PLAYER_HEARTBEAT_TIMEOUT = 0.5;

    // After how many minutes a closed game gets deleted automatically when it hasn't been killed normally yet.
    const GAME_MAX_TIME = 11;

    // In Seconds
    const CREATED_GAME_TIMEOUT = 30;

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
    public function handle(): void
    {
        $log = Log::channel('matchmaking_cleanup');

        // Delete queued players that haven't sent a queue resquest in the given period of time,
        // which means they crashed or closed the game without sending cancle.
        $cleanedQueuedPlayers = QueuedPlayer::where('updated_at', '<', Carbon::now()->subSeconds(static::PLAYER_HEARTBEAT_TIMEOUT * 60))
            ->delete();

        $log->info('Queued players: ' . json_encode($cleanedQueuedPlayers, JSON_PRETTY_PRINT));

        $closedGamesToKill =  Game::where('status', '=', MatchStatus::Closed->value)
            ->where('updated_at', '<', Carbon::now()->subSeconds(static::GAME_MAX_TIME * 60))
            ->get();

        foreach ($closedGamesToKill as $game) {
            //archive the games set to closed, so we don't lose the chat history
            $game->archiveGame(Faction::None);
            $game->status = MatchStatus::Killed;
            $game->save();
        }

        $log->info('Closed games set to Killed: ' . json_encode($closedGamesToKill->getQueueableIds(), JSON_PRETTY_PRINT));

        // delete games where the hunter crashed on loading into the arena, leaving the game stuck at created
        $deletedCreatedGames = Game::where('status', '=', MatchStatus::Created)
            ->where('created_at', '<', Carbon::now()->subSeconds(static::CREATED_GAME_TIMEOUT))
            ->delete();

        $log->info('Deleted Stuck Created games: ' . json_encode($deletedCreatedGames, JSON_PRETTY_PRINT));

        // Select User Ids that joined a game and haven't sent the match request for a period of time
        // This porpably means they crashed or never joined the game in the first place.
        $usersToRemove = DB::table('game_user')->join('games', 'game_user.game_id', '=', 'games.id')
            ->whereIn('games.status', [
                MatchStatus::Created->value,
                MatchStatus::Opened->value,
            ])
            ->where('game_user.updated_at', '<', Carbon::now()->subSeconds(static::PLAYER_HEARTBEAT_TIMEOUT * 60))
            ->get(['user_id']);

        $userIdArray = [];
        foreach ($usersToRemove as $user) {
            $userIdArray[] = $user->user_id;
        }

        $log->info('Deleting players that are in a mach, but didnt send a heartbeat: ' . json_encode($usersToRemove, JSON_PRETTY_PRINT));

        // Delete a game if one of the to be removed players is the host.
        Game::whereIn('creator_user_id', $userIdArray)->delete();

        // Delete them afterwards.
        DB::table('game_user')->whereIn('user_id', $userIdArray)->delete();
    }
}
