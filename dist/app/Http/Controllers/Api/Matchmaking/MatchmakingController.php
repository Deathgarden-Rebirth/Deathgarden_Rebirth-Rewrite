<?php

namespace App\Http\Controllers\Api\Matchmaking;

use App\Enums\Game\Matchmaking\MatchStatus;
use App\Enums\Game\Matchmaking\QueueStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Matchmaking\EndOfMatchRequest;
use App\Http\Requests\Api\Matchmaking\PlayerEndOfMatchRequest;
use App\Http\Requests\Api\Matchmaking\QueueRequest;
use App\Http\Requests\Api\Matchmaking\RegisterMatchRequest;
use App\Http\Requests\Metrics\MatchmakingRequest;
use App\Http\Responses\Api\Matchmaking\MatchData;
use App\Http\Responses\Api\Matchmaking\QueueData;
use App\Http\Responses\Api\Matchmaking\QueueResponse;
use App\Models\Admin\Archive\ArchivedGame;
use App\Models\Admin\Archive\ArchivedPlayerProgression;
use App\Models\Admin\Versioning\CurrentGameVersion;
use App\Models\Game\Matchmaking\Game;
use App\Models\Game\Matchmaking\QueuedPlayer;
use App\Models\User\User;
use Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MatchmakingController extends Controller
{
    const QUEUE_LOCK = 'queuedPlayers';

    public function getRegions()
    {
        return ["EU"];
    }

    public function queue(QueueRequest $request)
    {
        if($request->category !== CurrentGameVersion::get()?->gameVersion)
            abort(403, 'Too old mod version');

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
            $lock->release();
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

    public function deleteUserFromMatch(Game $match, User $user)
    {
        $requestUser = Auth::user();

        // Block request if it doesn't come from the host
        if($match->creator->id != $requestUser->id)
            return response('Action not allowed, you are not the creator of the match.', 403);

        $this->removeUserFromGame($user, $match);

        return json_encode(['success' => true]);
    }

    public function endOfMatch(EndOfMatchRequest $request)
    {
        if(ArchivedGame::archivedGameExists($request->matchId))
            return response('Match Already Closed', 209);

        $game = Game::find($request->matchId);
        $user = Auth::user();

        if($game->creator != $user)
            return response('you are not the creator of the match.', 403);

        $game->status = MatchStatus::Killed;
        $game->save();
        $game->archiveGame($request->dominantFaction);

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

        $lock = Cache::lock('playerEndOfMatch'.$user->id);

        try {
            // Lock the saving of the playerdata and stuff because the game can send multiple calls sometimes
            $lock->block(10 ,function () use (&$user, &$request, &$game) {
                if(ArchivedPlayerProgression::archivedPlayerExists($game->id, $user->id))
                    return;

                $playerData = $user->playerData();
                $characterData = $playerData->characterDataForCharacter($request->characterGroup->getCharacter());

                if($request->hasQuit)
                    $playerData->quitterState->addQuitterPenalty();
                else
                    $playerData->quitterState->addStayedMatch($playerData);

                $experienceSum = 0;

                foreach ($request->experienceEvents as $experienceEvent) {
                    $experienceSum += (int)$experienceEvent['amount'];
                }

                $characterData->addExperience($experienceSum);

                ++$characterData->readout_version;
                $characterData->save();

                $gainedCurrencyA = 0;
                $gainedCurrencyB = 0;
                $gainedCurrencyC = 0;

                foreach ($request->earnedCurrencies as $earnedCurrency) {
                    switch ($earnedCurrency['currencyName']) {
                        case 'CurrencyA':
                            $gainedCurrencyA += $earnedCurrency['amount'];
                            break;
                        case 'CurrencyB':
                            $gainedCurrencyB += $earnedCurrency['amount'];
                            break;
                        case 'CurrencyC':
                            $gainedCurrencyC += $earnedCurrency['amount'];
                    }
                }

                $playerData->currency_a += $gainedCurrencyA;
                $playerData->currency_b += $gainedCurrencyB;
                $playerData->currency_c += $gainedCurrencyC;

                ++$playerData->readout_version;
                $playerData->push();

                ArchivedPlayerProgression::archivePlayerProgression(
                    $game,
                    $user,
                    $request->hasQuit,
                    $request->characterGroup->getCharacter(),
                    $request->characterState,
                    $experienceSum,
                    $request->experienceEvents,
                    $gainedCurrencyA,
                    $gainedCurrencyB,
                    $gainedCurrencyC,
                );
            });
        } catch (LockTimeoutException $e) {
            return response('The Player end of match request for this user is currently being processed', 409);
        }

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

            // Remove user from any game he has previously joined.
            if($user->activeGames()->exists()) {
                $games = $user->activeGames()->get();
                foreach ($games as $game) {
                    $this->removeUserFromGame($user, $game);
                }
            }

            // Delete any active matches where the newly queued user is the creator.
            Game::where('creator_user_id', '=', $user->id)
                ->whereIn('status', [MatchStatus::Opened, MatchStatus::Created])
                ->delete();

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

    protected function removeUserFromGame(User $user, Game $game)
    {
        $game->players()->detach($user);

        if($game->players->count() !== 0)
            return;

        $game->delete();
    }

}
