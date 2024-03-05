<?php

namespace App\Http\Controllers\Api\Matchmaking;

use App\Classes\Matchmaking\MatchmakingPlayerCount;
use App\Enums\Game\Matchmaking\MatchmakingSide;
use App\Enums\Game\Matchmaking\MatchStatus;
use App\Enums\Game\Matchmaking\QueueStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Matchmaking\QueueRequest;
use App\Http\Requests\Api\Matchmaking\RegisterMatchRequest;
use App\Http\Responses\Api\Matchmaking\MatchData;
use App\Http\Responses\Api\Matchmaking\MatchProperties;
use App\Http\Responses\Api\Matchmaking\QueueData;
use App\Http\Responses\Api\Matchmaking\QueueResponse;
use App\Models\Game\Matchmaking\Game;
use App\Models\Game\Matchmaking\MatchConfiguration;
use App\Models\Game\Matchmaking\QueuedPlayer;
use Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class MatchmakingController extends Controller
{
    const TRY_CREATE_MATCH_INTERVAL_SECONDS = 5;

    public function getRegions()
    {
        return ["EU"];
    }

    public function queue(QueueRequest $request)
    {
        $this->processQueue();
        if($request->checkOnly)
            return json_encode($this->checkQueueStatus($request));

        return json_encode($this->addPlayerToQueue($request));
    }

    public function matchInfo(string $matchId)
    {
        $foundGame = Game::find($matchId);

        if($foundGame === null)
            return ['status' => 'Error', 'message' => 'Match not found.'];

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

	public function quit($matchId)
	{
		$foundGame = Game::find($matchId);
		$foundGame->status = MatchStatus::Completed;
		$foundGame->save();

		return json_encode(MatchData::fromGame($foundGame));
	}

	public function kill($matchId)
	{
		$foundGame = Game::find($matchId);
		$foundGame->status = MatchStatus::Killed;
		$foundGame->save();

		return json_encode(MatchData::fromGame($foundGame));
	}

    public function seedFileGet()
    {

    }

    public function seedFilePost()
    {

    }

    protected function checkQueueStatus(QueueRequest $request): QueueResponse
    {
        $user = Auth::user();
        $foundQueuedPlayer = QueuedPlayer::firstWhere('user_id', '=', $user->id);

        // If we found a queued Player, return his status
        if($foundQueuedPlayer !== null) {
            $response = new QueueResponse();
            $response->status = QueueStatus::Queued;
            $response->queueData = new QueueData(
                1,
                10
            );

            return $response;
        }
        // If we didn't find the player in the queued list, search if he is already joined a match

        $foundGame = $user->games();
        // If he also isn't in a game, add hi to the queue again
        if($foundGame->count() < 1) {
            $this->addPlayerToQueue($request);
            $response = new QueueResponse();
            $response->status = QueueStatus::Queued;
            $response->queueData = new QueueData(
                1,
                10
            );

            return $response;
        }

        /** @var Game $foundGame */
        $foundGame = $foundGame->first();
        $response = new QueueResponse();

        if($foundGame->status === MatchStatus::Opened) {
            $response->queueData = new QueueData(
                1,
                10
            );
            $response->status = QueueStatus::Matched;
        }
        else
            $response->status = QueueStatus::Matched;

        $response->matchData = MatchData::fromGame($foundGame);

        return $response;
    }

    protected function addPlayerToQueue(QueueRequest $request)
    {
        $user = Auth::user();
        if($user->games()->exists())
            return $this->checkQueueStatus($request);

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

        return $this->checkQueueStatus($request);
    }

    protected function processQueue(): void
    {
        // skip when the last time this job run is not older than the interval seconds
        if(!(Cache::get('tryCreateMatch', 0) < time() - static::TRY_CREATE_MATCH_INTERVAL_SECONDS))
            return;

        Cache::set('tryCreateMatch', time());

        // Select all queued Players/party leaders, descending by party size
        $players = QueuedPlayer::withCount('followingUsers')
            ->sharedLock()
            ->whereNull('queued_player_id')
            ->orderByDesc('following_users_count')
            ->orderByDesc('created_at')
            ->get();

        $playerCount = $this->getTotalPlayersCount($players);
        $availableMatchConfigs = MatchConfiguration::getAvailableMatchConfigs($playerCount->runners, $playerCount->hunters);

        if($availableMatchConfigs->isEmpty())
            return;

        $selectedConfig = MatchConfiguration::selectRandomConfigByWeight($availableMatchConfigs);

        // Should never happen, but just to be careful
        if($selectedConfig === null)
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

        $hunterGroupsSet = $this->determineMatchingPlayers($hunters, $selectedConfig->hunters);
        $runnerGroupsSet = $this->determineMatchingPlayers($runners, $selectedConfig->runners);

        // if we cannot create a match with our current player groups, stop
        if(count($runnerGroupsSet) == 0 || count($hunterGroupsSet) == 0)
            return;

        rsort($runnerGroupsSet, SORT_NUMERIC);
        rsort($hunterGroupsSet, SORT_NUMERIC);

        $newGame = new Game();
        $newGame->status = MatchStatus::Created;
        $newGame->matchConfiguration()->associate($selectedConfig);
        $newGame->save();

        foreach ($hunterGroupsSet as $groupSize) {
            $foundQueuedPlayer = $hunters->search(function (QueuedPlayer $hunter) use ($groupSize) {
                return ($hunter->following_users_count + 1) === $groupSize;
            });

            $newGame->addQueuedPlayer($hunters[$foundQueuedPlayer]);
        }

        foreach ($runnerGroupsSet as $groupSize) {
            $foundQueuedPlayer = $hunters->search(function (QueuedPlayer $runner) use ($groupSize) {
                return ($runner->following_users_count + 1) === $groupSize;
            });

            $newGame->addQueuedPlayer($runners[$foundQueuedPlayer]);
        }

        $newGame->determineHost();
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
