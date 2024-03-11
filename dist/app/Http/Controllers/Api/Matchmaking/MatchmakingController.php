<?php

namespace App\Http\Controllers\Api\Matchmaking;

use App\Classes\Matchmaking\MatchmakingPlayerCount;
use App\Enums\Game\Matchmaking\MatchmakingSide;
use App\Enums\Game\Matchmaking\MatchStatus;
use App\Enums\Game\Matchmaking\QueueStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Matchmaking\EndOfMatchRequest;
use App\Http\Requests\Api\Matchmaking\PlayerEndOfMatchRequest;
use App\Http\Requests\Api\Matchmaking\QueueRequest;
use App\Http\Requests\Api\Matchmaking\RegisterMatchRequest;
use App\Http\Requests\Metrics\MatchmakingRequest;
use App\Http\Responses\Api\Matchmaking\MatchData;
use App\Http\Responses\Api\Matchmaking\MatchProperties;
use App\Http\Responses\Api\Matchmaking\QueueData;
use App\Http\Responses\Api\Matchmaking\QueueResponse;
use App\Models\Game\CharacterData;
use App\Models\Game\Matchmaking\Game;
use App\Models\Game\Matchmaking\MatchConfiguration;
use App\Models\Game\Matchmaking\QueuedPlayer;
use App\Models\User\User;
use Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Prompts\Table;

class MatchmakingController extends Controller
{
    const TRY_CREATE_MATCH_INTERVAL_SECONDS = 5;

    // After how many minutes a queued palyer gets removed from  the queue or from an open match in Minutes.
    const PLAYER_HEARTBEAT_TIMEOUT = 2;

    // After how many minutes a closed game gets deleted automatically when it hasn't been killed normally yet.
    const GAME_MAX_TIME = 15;

    const QUEUE_LOCK = 'queuedPlayers';

    public function getRegions()
    {
        return ["EU"];
    }

    public function queue(QueueRequest $request)
    {
        $this->processQueue();
        $this->cleanupDeadPlayersAndGames();
        if($request->checkOnly)
            return json_encode($this->checkQueueStatus($request));

        return json_encode($this->addPlayerToQueue($request));
    }

    public function cancelQueue(MatchmakingRequest $request)
    {
        $user = Auth::user();

        if($user->id !== $request->playerId || $request->endState !== 'Cancelled')
            return response('', 204);

        $lock = Cache::lock(static::QUEUE_LOCK, 10);

        try {
            $lock->block(20 ,function () use (&$user) {
                // Delete the player from the Queue
                QueuedPlayer::where('user_id', '=', $user->id)->delete();
                // And also from any game they are matched for.
                DB::table('game_user')->where('user_id', '=', $user->id)->delete();
            });
        } catch (LockTimeoutException $e) {
            Log::channel('matchmaking')->emergency('Queue Cancel: Could not acquire Lock for canceling user '.$user->id.'('.$user->last_known_username.')');
        } finally {
            $lock?->release();
        }

        return response('', 204);
    }

    public function matchInfo(string $matchId)
    {
        $foundGame = Game::find($matchId);

        if($foundGame === null)
            return ['status' => 'Error', 'message' => 'Match not found.'];

        $user = Auth::user();
        // Update timestamp for player heartbeat check
        $foundGame->players()->updateExistingPivot($user->id, [
            'updated_at' => Carbon::now(),
        ]);
        $foundGame->updated_at = Carbon::now();
        $foundGame->save();

        $this->cleanupDeadPlayersAndGames();

        $response = MatchData::fromGame($foundGame);
        return json_encode($response);
    }

    public function register(RegisterMatchRequest $request, string $matchId)
    {
        $foundGame = Game::find($matchId);
        $foundGame->session_settings = $request->sessionSettings;
        $foundGame->status = MatchStatus::Opened;
        $foundGame->save();

        return json_encode(MatchData::fromGame($foundGame));
    }

	public function close($matchId)
	{
		$foundGame = Game::find($matchId);
		$foundGame->status = MatchStatus::Closed;
		$foundGame->save();

		return json_encode(MatchData::fromGame($foundGame));
	}

    /*
     * Set a game to Quit state, Maybe exists but unsure since it never showed up in the request logs.
     */
	public function quit($matchId)
	{
		$foundGame = Game::find($matchId);
        $user = Auth::user();

        if($foundGame->creator === $user) {
            $foundGame->status = MatchStatus::Destroyed;
            $foundGame->save();
        }
        else {
            $foundGame->players()->detach(Auth::user()->id);
        }

        return response(null, 204);
	}

	public function kill($matchId)
	{
		$foundGame = Game::find($matchId);

        $response = json_encode(MatchData::fromGame($foundGame));
        $foundGame->delete();

		return $response;
	}

    public function seedFileGet(string $gameVersion, string $seed, string $mapName)
    {
        return response('', 200);
    }

    public function seedFilePost(string $gameVersion, string $seed, string $mapName)
    {
        return response('', 200);
    }

    public function deleteUserFromMatch(string $matchId, string $userId)
    {
        $foundGame = Game::find($matchId);
        $userToRemove = User::find($userId);
        $requestUser = Auth::user();

        // Block request if it doesn't come from the host
        if($foundGame === null || $foundGame->creator != $requestUser)
            return response('Action not allowed, you are not the creator of the match.', 403);

        $foundGame->players()->detach($userToRemove);
    }

    public function endOfMatch(EndOfMatchRequest $request)
    {
        $game = Game::find($request->matchId);
        $user = Auth::user();

        if($game->creator != $user)
            return response('you are not the creator of the match.', 403);

        $game->status = MatchStatus::Killed;
        $game->save();

        return json_encode(['success' => true]);
    }

    public function playerEndOfMatch(PlayerEndOfMatchRequest $request)
    {
        $user = Auth::user();
        $game = Game::find($request->matchId);

        if($game === null)
            return response('Match not found.', 404);

        if ($game->creator != $user)
            throw new AuthorizationException('User is not host of given match');

        $user = User::find($request->playerId);

        if($user === null)
            return response('User not found.', 404);

        $playerData = $user->playerData();
        $characterData = $playerData->characterDataForCharacter($request->characterGroup->getCharacter());

        foreach ($request->experienceEvents as $experienceEvent) {
            $characterData->addExperience($experienceEvent['amount']);
        }

        ++$characterData->readout_version;
        $characterData->save();

        foreach ($request->earnedCurrencies as $earnedCurrency) {
            switch ($earnedCurrency['currencyName']) {
                case 'CurrencyA':
                    $playerData->currency_a += $earnedCurrency['amount'];
                    break;
                case 'CurrencyB':
                    $playerData->currency_b += $earnedCurrency['amount'];
                    break;
                case 'CurrencyC':
                    $playerData->currency_c += $earnedCurrency['amount'];
            }
        }

        ++$playerData->readout_version;
        $playerData->save();

        $this->removeUserFromGame($user, $game);

        // We dont really know what the game wants except for a json object called "player".
        return json_encode(['player' => []], JSON_FORCE_OBJECT);
    }

    protected function checkQueueStatus(QueueRequest $request): QueueResponse
    {
        $user = Auth::user();
        $foundQueuedPlayer = QueuedPlayer::firstWhere('user_id', '=', $user->id);

        // If we found a queued Player, return his status
        if($foundQueuedPlayer !== null) {
            // Set Last queue call time to remove players that maybe crashed or something
            // if they haven't sent a queue request in a long time.
            $foundQueuedPlayer->updated_at = Carbon::now();
            $foundQueuedPlayer->save();

            $response = new QueueResponse();
            $response->status = QueueStatus::Queued;
            $response->queueData = new QueueData(
                1,
                10
            );

            return $response;
        }

        // If we didn't find the player in the queued list, search if he is already joined a match
        // Only search for Created or open matches
        $foundGame = $user->activeGames();
        // If they also aren't in a game, add them to the queue again
        if($foundGame->count() < 1) {
            $this->addPlayerToQueue($request);
            $response = new QueueResponse();
            $response->status = QueueStatus::Queued;
            $response->queueData = new QueueData(
                1,
            );

            return $response;
        }

        /** @var Game $foundGame */
        $foundGame = $foundGame->first();

        // Update pivot updated_at to detect client crashes or other errors of theyhavent sent a request in a period of time.
        $foundGame->players()->updateExistingPivot($user->id, ['updated_at' => Carbon::now()]);

        $response = new QueueResponse();

        if($foundGame->status === MatchStatus::Opened) {
            $response->queueData = new QueueData(
                1,
                1
            );
        }
        $response->status = QueueStatus::Matched;
        $response->matchData = MatchData::fromGame($foundGame);

        return $response;
    }

    protected function addPlayerToQueue(QueueRequest $request)
    {
        Cache::lock(static::QUEUE_LOCK, 10)->block(20, function () use ($request) {
            $user = Auth::user();
            if($user->activeGames()->exists())
                return;

            $queued = QueuedPlayer::firstOrCreate(['user_id' => $user->id]);
            $queued->leader()->disassociate();
            $queued->side = $request->side;
            $queued->user()->associate($user->id);
            $queued->save();

            foreach ($request->additionalUserIds as $additionalUserId) {
                $follower = QueuedPlayer::firstOrCreate(['user_id' => $additionalUserId]);
                $follower->side = $request->side;
                $follower->user()->associate($additionalUserId);
                $follower->save();
                $queued->followingUsers()->save($follower);
            }
        });

        return $this->checkQueueStatus($request);
    }

    protected function processQueue(): void
    {
        // skip when the last time this job run is not older than the interval seconds
        if(!(Cache::get('tryCreateMatch', 0) < time() - static::TRY_CREATE_MATCH_INTERVAL_SECONDS))
            return;

        $lock = Cache::lock(static::QUEUE_LOCK, 20);

        // If we cannot acquire the lock, do nothing
        if(!$lock->get())
            return;

        // Select all queued Players/party leaders, descending by party size
        $players = QueuedPlayer::withCount('followingUsers')
            ->sharedLock()
            ->whereNull('queued_player_id')
            ->orderByDesc('following_users_count')
            ->orderBy('created_at')
            ->get();

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

        if($availableMatchConfigs->isEmpty()) {
            $lock->release();
            return;
        }

        $selectedConfig = MatchConfiguration::selectRandomConfigByWeight($availableMatchConfigs);

        // Should never happen, but just to be careful
        if($selectedConfig === null) {
            $lock->release();
            return;
        }

        $hunterGroupsSet = $this->determineMatchingPlayers($hunters, $selectedConfig->hunters);
        $runnerGroupsSet = $this->determineMatchingPlayers($runners, $selectedConfig->runners);

        // if we cannot create a match with our current player groups, stop
        if($runnerGroupsSet === false || $hunterGroupsSet === false) {
            $lock->release();
            return;
        }
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

        // Set cached timeat the end of processing and release lock
        Cache::set('tryCreateMatch', time());
        $lock->release();
    }

    protected function tryFillOpenGames(Collection|array &$hunters, Collection|array &$runners)
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

    protected function removeUserFromGame(User $user, Game $game)
    {
        $game->players()->detach($user);

        if($game->players->count() !== 0)
            return;

        $game->status = MatchStatus::Killed;
        $game->save();
    }

    protected function cleanupDeadPlayersAndGames(): void
    {
        // skip when the last time this job ran is not older than the interval seconds
        if(!(Cache::get('cleanupDeadPlayersAndGames', 0) < time() - static::PLAYER_HEARTBEAT_TIMEOUT))
            return;

        // Delete queued players that haven't sent a queue resquest in the given period of time,
        // which means they crashed or closed the game without sending cancle.
        QueuedPlayer::where('updated_at', '<', Carbon::now()->subMinutes(static::PLAYER_HEARTBEAT_TIMEOUT))
            ->delete();

        Game::where('status', '=', MatchStatus::Closed->value)
            ->where('updated_at', '<', Carbon::now()->subMinutes(static::GAME_MAX_TIME))
            ->delete();

        // Delete users that joined a game and haven't sent the match request for a period of time
        // This porpably means they crashed or never joined the game in the first place.
        DB::table('game_user')->join('games', 'game_user.game_id', '=', 'games.id')
            ->whereIn('games.status', [
                MatchStatus::Created->value,
                MatchStatus::Opened->value,
            ])
            ->where('game_user.updated_at', '<', Carbon::now()->subMinutes(static::PLAYER_HEARTBEAT_TIMEOUT))
            ->delete();
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
}
