<?php

namespace App\Http\Controllers\Api\Player;

use App\Http\Controllers\Controller;
use App\Http\Middleware\AccessLogger;
use App\Http\Requests\Api\Player\Inbox\ClaimInboxMessageRequest;
use App\Http\Responses\Api\Player\Inbox\InboxMessageClaimedReward;
use App\Http\Responses\Api\Player\Inbox\InboxMessageClaimResponse;
use App\Models\Game\Inbox\InboxMessage;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class InboxController extends Controller
{
    public function count(Request $request) {
        if (!Auth::check())
            abort(403, 'You are not logged in');

        $flag = $request->input('flag');
        $user = Auth::user();

        $query = $user->inboxMessages()->where(function ($query) {
            $query->where('expire_at', '>', Carbon::now())->orWhereNull('expire_at');
        });

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
        $messages = $user->inboxMessages()
            ->where('expire_at', '>', Carbon::now())
            ->orWhereNull('expire_at')
            ->paginate($limit);

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

        $idsToDelete = [];
        foreach($messages as $message) {
            try {
                $idsToDelete[] = Carbon::createFromTimestampMs($message['received'])->toDateTimeString();
            } catch(\Exception $e) {
                $logger = AccessLogger::getSessionLogConfig();
                $logger->warning($request->method().' '.$request->getUri().': Something Went Wrong, Messagelist: '.json_encode($messages, JSON_PRETTY_PRINT));
                return ['success' => false];
            }
        }

        $user->inboxMessages()->whereIn('id', $idsToDelete)->delete();
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

        $timestampsToSet = [];

        foreach($messages as $message) {
            try {
                $timestampsToSet[] = Carbon::createFromTimestampMs($message['received'])->toDateTimeString();
            } catch(\Exception $e) {
                $logger = AccessLogger::getSessionLogConfig();
                $logger->warning($request->method().' '.$request->getUri().': Something Went Wrong, Messagelist: '.json_encode([
                        'exception' => $e,
                        'messages' => $messages,
                    ], JSON_PRETTY_PRINT));
                return ['success' => false];
            }
        }

        $user->inboxMessages()->whereIn('received', $timestampsToSet)->update(['flag' => $flag]);

        $resultList = [];
        foreach($timestampsToSet as $timestamp) {
            $resultList[] = [
                'received' => Carbon::parse($timestamp)->getTimestampMs(),
                'success' => true,
                'recipientId' => $user->id,
            ];
        }

        return [
            'List' => $resultList,
        ];
    }

    public function claimMessage(ClaimInboxMessageRequest $request)
    {
        $user = Auth::user();
        /** @var InboxMessage $message */
        $message = $user->inboxMessages()
            ->where('user_id', '=', $user->id)
            ->where('received', '=', Carbon::createFromTimestampMs($request->receivedTimestamp)->toDateTimeString())
            ->first();

        if($message === null)
            abort(404, 'Inbox message not found');

        if($message->has_claimed)
            return json_encode(new InboxMessageClaimResponse());

        $claimable = $message->getClaimables();
        $playerData = $user->playerData();

        $response = new InboxMessageClaimResponse();

        foreach($claimable as $reward) {
            if($reward->rewardType === 'Currency') {
                switch ($reward->id) {
                    case 'CurrencyA':
                        $oldAmount = $playerData->currency_a;
                        $playerData->currency_a += $reward->amount;
                        $newAmount = $playerData->currency_a;
                        break;
                    case 'CurrencyB':
                        $oldAmount = $playerData->currency_b;
                        $playerData->currency_b += $reward->amount;
                        $newAmount = $playerData->currency_b;
                        break;
                    case 'CurrencyC':
                        $oldAmount = $playerData->currency_c;
                        $playerData->currency_c += $reward->amount;
                        $newAmount = $playerData->currency_c;
                        break;
                    default:
                        continue 2;
                }

                $response->currencies[] = new InboxMessageClaimedReward(
                    $reward->id,
                    $newAmount,
                    $newAmount - $oldAmount,
                );
            }
            else if($reward->rewardType === 'Inventory') {
                $itemUuid = Uuid::fromString($reward->id)->toString();

                try {
                    $playerData->inventory()->attach($itemUuid);
                    // Since we can only have one occurrence of an item in the inventory, and an exception gets thrown when we try to add the same item twice
                    // we can just hard code the new and received amount to 1.
                    $response->inventories[] = new InboxMessageClaimedReward(
                        $reward->id,
                        1,
                        1,
                    );
                } catch(UniqueConstraintViolationException $e) {
                    $response->inventories[] = new InboxMessageClaimedReward(
                        $reward->id,
                        1,
                        0,
                    );
                }
            }
        }

        $playerData->save();
        $message->has_claimed = true;
        $message->save();

        return json_encode($response);
    }

}
