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
        if($players->isEmpty())
            return;

        $runners = new Collection();
        $hunters = new Collection();

        // Split hunters and runners into separate collections
        $players->each(function (QueuedPlayer $player) use ($hunters, $runners) {
            if($player->side === MatchmakingSide::Hunter)
                $hunters->add($player);
            else
                $runners->add($player);
        });

        $this->tryFillOpenGames($hunters, $runners);

        $playerCount = $this->getTotalPlayersCount($players);
        $availableMatchConfigs = MatchConfiguration::getAvailableMatchConfigs($playerCount->runners, $playerCount->hunters);

        if($availableMatchConfigs->isEmpty())
            return;

        $selectedConfig = MatchConfiguration::selectRandomConfigByWeight($availableMatchConfigs);

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

        foreach ($openGames as $game) {
            $neededPlayers = $game->remainingPlayerCount();

            // game is full and doesn't need filling
            if($neededPlayers->getTotal() == 0)
                continue;

            if($neededPlayers->hunters > 0) {
                $hunterGroupsSet = $this->determineMatchingPlayers($hunters, $neededPlayers->hunters);

                // see if there are any group combinations possible to fill the game
                if($hunterGroupsSet === false)
                    continue;

                // use biggest groups first
                rsort($hunterGroupsSet, SORT_NUMERIC);

                foreach ($hunterGroupsSet as $groupSize) {
                    $foundQueuedPlayerIndex = $hunters->search(function (QueuedPlayer $hunter) use ($groupSize) {
                        return ($hunter->following_users_count + 1) === $groupSize;
                    });

                    $foundHunter = $hunters->pull($foundQueuedPlayerIndex);
                    $game->addQueuedPlayer($foundHunter);
                }
            }

            if($neededPlayers->runners > 0) {
                $runnerGroupSet = $this->determineMatchingPlayers($runners, $neededPlayers->runners);

                // see if there are any group combinations possible to fill the game
                if($runnerGroupSet === false)
                    continue;

                // use biggest groups first
                rsort($runnerGroupSet, SORT_NUMERIC);

                foreach ($runnerGroupSet as $groupSize) {
                    $foundQueuedPlayerIndex = $runners->search(function (QueuedPlayer $runner) use ($groupSize) {
                        return ($runner->following_users_count + 1) === $groupSize;
                    });

                    $foundRunner = $runners->pull($foundQueuedPlayerIndex);
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
}
