<?php

namespace App\Console\Commands;

use App\Classes\Matchmaking\MatchmakingPlayerCount;
use App\Enums\Game\Matchmaking\MatchmakingSide;
use App\Enums\Game\Matchmaking\MatchStatus;
use App\Models\Game\Matchmaking\Game;
use App\Models\Game\Matchmaking\MatchConfiguration;
use App\Models\Game\Matchmaking\QueuedPlayer;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Log;
use Psr\Log\LoggerInterface;

class ProcessMatchmaking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'matchmaking:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected static LoggerInterface $log;

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // Select all queued Players/party leaders, descending by party size
        $players = QueuedPlayer::withCount('followingUsers')
            ->sharedLock()
            ->whereNull('queued_player_id')
            ->orderByDesc('following_users_count')
            ->orderBy('created_at')
            ->get();

        // If there are no players in the queue, stop here.
        if($players->isEmpty()){
            static::log()->info('No Users in Queue, Stopping');
            return;
        }

        $runners = new Collection();
        $hunters = new Collection();

        // Split hunters and runners into separate collections
        $players->each(function (QueuedPlayer $player) use ($hunters, $runners) {
            if($player->side === MatchmakingSide::Hunter)
                $hunters->add($player);
            else
                $runners->add($player);
        });

        static::log()->info('Users in Queue:'. json_encode([
                'hunters' => $hunters->toArray(),
                'runners' => $runners->toArray(),
            ],
            JSON_PRETTY_PRINT
        ));

        $this->tryFillOpenGames($hunters, $runners);

        static::log()->info('Users in Queue after trying to fill:'. json_encode([
                'hunters' => $hunters->toArray(),
                'runners' => $runners->toArray(),
            ],
                JSON_PRETTY_PRINT
            ));

        $playerCount = $this->getTotalPlayersCount($players);
        if ($playerCount->hunters === 1 && ($playerCount->runners === 4 || $playerCount->runners === 5))
            sleep(10);
        $availableMatchConfigs = MatchConfiguration::getAvailableMatchConfigs($playerCount->runners, $playerCount->hunters);

        if($availableMatchConfigs->isEmpty())
            return;

        $selectedConfig = MatchConfiguration::selectMatchConfig($availableMatchConfigs, $playerCount->runners, $playerCount->hunters);

        // Should never happen, but just to be careful
        if($selectedConfig === null)
            return;

        $hunterGroupsSet = $this->determineMatchingPlayers($hunters, $selectedConfig->hunters);
        $runnerGroupsSet = $this->determineMatchingPlayers($runners, $selectedConfig->runners);

        // if we cannot create a match with our current player groups, stop
        if($runnerGroupsSet === false || $hunterGroupsSet === false)
            return;

        rsort($runnerGroupsSet, SORT_NUMERIC);
        rsort($hunterGroupsSet, SORT_NUMERIC);

        $newGame = new Game();
        $newGame->status = MatchStatus::Created;
        $newGame->matchConfiguration()->associate($selectedConfig);
        $newGame->save();

        static::log()->info('New game created: '. json_encode($newGame->toArray(), JSON_PRETTY_PRINT));

        foreach ($hunterGroupsSet as $groupSize) {
            $foundQueuedPlayerIndex = $hunters->search(function (QueuedPlayer $hunter) use ($groupSize) {
                return ($hunter->following_users_count + 1) === $groupSize;
            });

            $foundHunter = $hunters->pull($foundQueuedPlayerIndex);
            $newGame->addQueuedPlayer($foundHunter);
        }

        foreach ($runnerGroupsSet as $groupSize) {
            $foundQueuedPlayerIndex = $runners->search(function (QueuedPlayer $runner) use ($groupSize) {
                return ($runner->following_users_count + 1) === $groupSize;
            });
            $foundRunner = $runners->pull($foundQueuedPlayerIndex);
            $newGame->addQueuedPlayer($foundRunner);
        }

        $newGame->determineHost();
    }

    protected function tryFillOpenGames(Collection|array &$hunters, Collection|array &$runners): void
    {
        $openGames = Game::where('status', '=', MatchStatus::Opened->value)->get();

        static::log()->info('Found Open Matches:' . json_encode($openGames->toArray(),JSON_PRETTY_PRINT));

        foreach ($openGames as $game) {
            static::log()->info("Game $game->id current players: ". json_encode($game->players));
            $neededPlayers = $game->remainingPlayerCount();

            // game is full and doesn't need filling
            if($neededPlayers->getTotal() == 0)
                continue;

            if($neededPlayers->hunters > 0) {
                static::log()->info("Game $game->id needs a hunter");
                $hunterGroupsSet = $this->determineMatchingPlayers($hunters, $neededPlayers->hunters);

                // see if there are any group combinations possible to fill the game
                if($hunterGroupsSet === false)
                    continue;

                // use biggest groups first
                rsort($hunterGroupsSet, SORT_NUMERIC);
                static::log()->info("Game $game->id Try fill game group sets: ". json_encode($runnerGroupSet));

                foreach ($hunterGroupsSet as $groupSize) {
                    $foundQueuedPlayerIndex = $hunters->search(function (QueuedPlayer $hunter) use ($groupSize) {
                        return ($hunter->following_users_count + 1) === $groupSize;
                    });

                    $foundHunter = $hunters->pull($foundQueuedPlayerIndex);
                    static::log()->info('Filled hunter slot on open game.'. json_encode([
                            'hunter' => $foundHunter,
                            'game' => $game,
                        ],
                        JSON_PRETTY_PRINT)
                    );
                    $game->addQueuedPlayer($foundHunter);
                }
            }

            if($neededPlayers->runners > 0) {
                static::log()->info("Game $game->id needs a runner");
                $runnerGroupSet = $this->determineMatchingPlayers($runners, $neededPlayers->runners);

                // see if there are any group combinations possible to fill the game
                if($runnerGroupSet === false)
                    continue;

                // use biggest groups first
                rsort($runnerGroupSet, SORT_NUMERIC);

                static::log()->info("Game $game->id Try fill game group sets: ". json_encode($runnerGroupSet));

                foreach ($runnerGroupSet as $groupSize) {
                    $foundQueuedPlayerIndex = $runners->search(function (QueuedPlayer $runner) use ($groupSize) {
                        return ($runner->following_users_count + 1) === $groupSize;
                    });

                    $foundRunner = $runners->pull($foundQueuedPlayerIndex);
                    static::log()->info('Filled runner slot on open game.'. json_encode([
                            'runner' => $foundRunner,
                            'game' => $game,
                        ],
                            JSON_PRETTY_PRINT)
                    );
                    $game->addQueuedPlayer($foundRunner);
                }
            }
        }
    }

    private function getTotalPlayersCount(Collection &$queuedPlayerCollection): MatchmakingPlayerCount
    {
        $count = new MatchmakingPlayerCount();
        foreach ($queuedPlayerCollection as $player) {
            /** @var QueuedPlayer $player */

            if($player->side == MatchmakingSide::Hunter)
                $count->hunters += $player->following_users_count + 1;
            else
                $count->runners += $player->following_users_count + 1;
        }
        return $count;
    }

    /**
     * @param Collection $queuedPlayers
     * @param int $target
     * @return array|false
     */
    private function determineMatchingPlayers(Collection &$queuedPlayers, int $target): array|false
    {
        $availableNumbers = [];
        $queuedPlayers->each(function (QueuedPlayer $player) use (&$availableNumbers) {
            $availableNumbers[] = $player->following_users_count + 1;
        });

        $result = MatchmakingPlayerCount::findSubsetsOfSum($availableNumbers, $target, true);

        if(count($result) > 0)
            return $result;
        return false;
    }

    public static function log(): LoggerInterface
    {
        return static::$log ?? static::$log = Log::channel('matchmaking');
    }
}
