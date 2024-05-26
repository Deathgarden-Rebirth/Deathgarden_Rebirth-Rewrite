<?php

namespace App\Http\Controllers\Api\Player;

use App\Http\Controllers\Controller;
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
            $message->flag = 'READ';
            $message->save();
        }

        if($messages->currentPage() < $messages->lastPage()) {
            $result['nextPage'] = $messages->currentPage() + 1;
        }

        return $result;
    }
}
