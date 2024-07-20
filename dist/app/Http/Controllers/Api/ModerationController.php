<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Player\CheckUsernameRequest;
use App\Http\Responses\Api\Player\CheckUsernameResponse;
use Illuminate\Http\Request;

class ModerationController extends Controller
{

    public function checkUsername(CheckUsernameRequest $request)
    {
        return json_encode(new CheckUsernameResponse($request->userId, $request->username));
    }

    public function checkChatMessage()
    {
        return ['message' => 'TEst'];
    }
}
