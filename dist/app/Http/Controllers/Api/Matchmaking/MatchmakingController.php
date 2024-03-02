<?php

namespace App\Http\Controllers\Api\Matchmaking;

use App\Enums\Game\Matchmaking\MatchmakingSide;
use App\Enums\Game\Matchmaking\QueueStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Matchmaking\QueueRequest;
use App\Http\Responses\Api\Matchmaking\MatchData;
use App\Http\Responses\Api\Matchmaking\MatchProperties;
use App\Http\Responses\Api\Matchmaking\QueueData;
use App\Http\Responses\Api\Matchmaking\QueueResponse;
use App\Models\Game\Matchmaking\Game;
use App\Models\Game\Matchmaking\QueuedPlayer;
use Auth;

class MatchmakingController extends Controller
{
    public function getRegions()
    {
        return ["EU"];
    }

    public function queue(QueueRequest $request)
    {
        if($request->checkOnly)
            return json_encode($this->checkQueueStatus($request));

        return json_encode($this->addPlayerToQueue($request));
    }

    protected function checkQueueStatus(QueueRequest $request): QueueResponse
    {
        $user = Auth::user();
        $foundQueuedPlayer = QueuedPlayer::firstWhere('user_id', '=', $user->id);

        if($foundQueuedPlayer !== null) {
            $response = new QueueResponse();
            $response->status = QueueStatus::Queued;
            $response->queueData = new QueueData(
                1,
                10
            );

            return $response;
        }

        $foundGame = $user->games();

        if($foundGame->count() < 1)
            return 'test';

        /** @var Game $foundGame */
        $foundGame = $foundGame->first();

        $response = new QueueResponse();
        $matchData = new MatchData();

        $matchData->matchId = $foundGame->id;
        $matchData->category = $request->category;
        $matchData->creationDateTime = $foundGame->created_at->getTimestamp();
        $matchData->status = $foundGame->status;
        $matchData->creator = $foundGame->creator->creator_user_id;

        $players = $foundGame->players;
        $matchData->players = $players->pluck('id')->toArray();
        $matchData->sideA = $players->where('pivot.side', MatchmakingSide::Hunter->value)->pluck('id')->toArray();
        $matchData->sideB = $players->where('pivot.side', MatchmakingSide::Runner->value)->pluck('id')->toArray();

        $matchData->props = new MatchProperties(
            $foundGame->matchConfiguration,
        );

        $response->matchData = $matchData;

        return $response;
    }

    protected function addPlayerToQueue(QueueRequest $request)
    {
        $user = Auth::user();
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
}
