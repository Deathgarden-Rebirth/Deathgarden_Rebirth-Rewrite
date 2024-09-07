<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Moderation\CheckChatMessageRequest;
use App\Http\Requests\Api\Moderation\ReportPlayerRequest;
use App\Http\Requests\Api\Player\CheckUsernameRequest;
use App\Http\Responses\Api\Player\CheckUsernameResponse;
use App\Models\Admin\BadChatMessage;
use App\Models\Game\Moderation\PlayerReport;
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
        // Lobby Host that send the request to check the message. We log him too since only the lobby host checks the messages in teh backend.
        $hostUser = Auth::user();
        // User that send the chat message
        $messageUser = User::find($request->userId);

        $checker = Builder::blocker($request->message);

        if($checker->clean())
            return response('{}');

        $this->logBadMessage($hostUser, $messageUser, $request->message);
        return response('Bad Message >:(');
    }

    public function logBadMessage(User $hostUser, User $messageUser, string $message): void {
        $badMessage = new BadChatMessage();
        $badMessage->hostUser()->associate($hostUser);
        $badMessage->user()->associate($messageUser);
        $badMessage->message = $message;
        $badMessage->save();
    }

    public function playerReport(ReportPlayerRequest $request) {
        $reportingUser = Auth::user();

        $report = new PlayerReport();

        $report->reason = $request->reason;
        $report->details = $request->details;
        $report->match_id = $request->matchId;
        $report->player_infos = $request->playerInfos->toArray();

        $report->reportingUser()->associate($reportingUser);
        $report->reportedUser()->associate($request->reportedPlayer);
        $report->save();

        return 'OK';
    }
}
