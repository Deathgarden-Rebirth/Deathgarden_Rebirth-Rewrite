<?php

namespace App\Http\Controllers\Api\Matchmaking;

use App\Enums\Game\Matchmaking\QueueStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Matchmaking\QueueRequest;
use App\Http\Responses\Api\Matchmaking\QueueData;
use App\Http\Responses\Api\Matchmaking\QueueResponse;
use Illuminate\Http\Request;

class MatchmakingController extends Controller
{
    public function getRegions()
    {
        return ["EU"];
    }

    public function queue(QueueRequest $request)
    {
        $response = new QueueResponse();
        $response->status = QueueStatus::Queued;
        $response->queueData = new QueueData(
            0,
            -100,
        );

        return json_encode($response);
    }
}
