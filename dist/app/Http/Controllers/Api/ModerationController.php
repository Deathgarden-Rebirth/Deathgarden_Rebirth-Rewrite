<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Moderation\CheckChatMessageRequest;
use App\Http\Requests\Api\Player\CheckUsernameRequest;
use App\Http\Responses\Api\Player\CheckUsernameResponse;
use App\Models\Admin\BadChatMessage;
use App\Models\User\User;
use ConsoleTVs\Profanity\Builder;
use Illuminate\Support\Facades\Auth;

class ModerationController extends Controller
{

    public function checkUsername(CheckUsernameRequest $request): mixed
    {
        return json_encode(new CheckUsernameResponse($request->userId, $request->username));
    }

    public function checkChatMessage(CheckChatMessageRequest $request): mixed
    {
        $checker = Builder::blocker($request->message);

        if($checker->clean())
            return response('{}');

        $this->logBadMessage(Auth::user(), $request->message);
        return response('Bad Message >:(');
    }

    public function logBadMessage(User $user, string $message): void {
        $badMessage = new BadChatMessage();
        $badMessage->user()->associate($user);
        $badMessage->message = $message;
        $badMessage->save();
    }
}
