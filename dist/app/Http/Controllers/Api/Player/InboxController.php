<?php

namespace App\Http\Controllers\Api\Player;

use App\Http\Controllers\Controller;
use App\Http\Middleware\AccessLogger;
use App\Models\Game\Inbox\InboxMessage;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class InboxController extends Controller
{
    public function count(Request $request) {
        if (!Auth::check())
            abort(403, 'You are not logged in');

        $flag = $request->input('flag');
        $user = Auth::user();

        //TODO: Add Model Retrieving logic
        $query = $user->inboxMessages();

        if($flag !== null)
            $query->where('flag', '=', $flag);

        $count = $query->count();
        return ['count' => $count];
    }

    public function list(Request $request) {
        if (!Auth::check())
            abort(403, 'You are not logged in');

        $limit = $request->input('limit', 100);
        $user = Auth::user();

        /** @var InboxMessage[]|Collection|LengthAwarePaginator $messages */
        $messages = $user->inboxMessages()->paginate($limit);

        $result = [
            'messages' => [],
        ];

        foreach ($messages as $message) {
            $result['messages'][] = $message->toMessageResponse();
            $message->save();
        }

        if($messages->currentPage() < $messages->lastPage()) {
            $result['nextPage'] = $messages->currentPage() + 1;
        }

        return $result;
    }

    public function deleteMultiple(Request $request) {
        if (!Auth::check())
            abort(403, 'You are not logged in');

        $messages = $request->input('messageList', false);
        $user = Auth::user();

        if($messages === false || !is_array($messages))
            abort(400, 'Missing Parameter "messageList" of type array');

        foreach($messages as $message) {
            try {
                $messageId = $message['received'];
                $user->inboxMessages()->delete($messageId);
            } catch(\Exception $e) {
                $logger = AccessLogger::getSessionLogConfig();
                $logger->warning($request->method().' '.$request->getUri().': Something Went Wrong, Messagelist: '.json_encode($messages, JSON_PRETTY_PRINT));
                return ['success' => false];
            }
        }

        return ['success' => true];
    }

    public function markMessages(Request $request) {
        if (!Auth::check())
            abort(403, 'You are not logged in');

        $messages = $request->input('messageList', false);
        $flag = $request->input('flag', false);
        $user = Auth::user();

        if($messages === false || !is_array($messages))
            abort(400, 'Missing Parameter "messageList" of type array.');

        if($flag === false)
            abort(400, 'Missing Parameter "flag" of type string.');

        $idsToSet = [];

        foreach($messages as $message) {
            try {
                $idsToSet[] = $message['received'];
            } catch(\Exception $e) {
                $logger = AccessLogger::getSessionLogConfig();
                $logger->warning($request->method().' '.$request->getUri().': Something Went Wrong, Messagelist: '.json_encode($messages, JSON_PRETTY_PRINT));
                return ['success' => false];
            }
        }

        $user->inboxMessages()->whereIn('id', $idsToSet)->update(['flag' => $flag]);

        $resultList = [];
        foreach($idsToSet as $id) {
            $resultList[] = [
                'received' => $id,
                'success' => true,
                'recipientId' => $user->id,
            ];
        }

        return [
            'List' => $resultList,
        ];
    }
}
