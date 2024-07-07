<?php

namespace App\Http\Controllers\Web\Admin\Tools;

use App\Enums\Auth\Permissions;
use App\Http\Requests\Api\Admin\Tools\MailerSendRequest;
use App\Http\Responses\Api\Player\Inbox\Message;
use App\Models\Game\Inbox\InboxMessage;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class InboxMailerController extends AdminToolController
{
    protected static string $name = 'Mailer';

    protected static string $description = 'Send inbox messages to one or multiple users.';
    protected static string $iconComponent = 'icons.mail';

    protected static Permissions $neededPermission = Permissions::INBOX_MAILER;

    public function index(Request $request)
    {
        $userIds = $request->input('users');
        if(is_array($userIds))
            $prefillUsers = User::whereIn('id', $userIds)->get();
        else
            $prefillUsers = [];

        return view('admin.tools.mailer',['preSelectedUsers' => $prefillUsers]);
    }

    public function send(MailerSendRequest $request) {
        $currentPage = 0;
        $now = Carbon::now(config('app.timezone'));
        $sentToUsers = [];

        $claimable = [];
        foreach ($request->rewards as $reward) {
            $claimable[] = (array)$reward;
        }

        do {
            ++$currentPage;
            $messages = [];
            /** @var Collection[]|LengthAwarePaginator $users */
            if($request->allUsers) {
                $users = User::paginate(1000, ['id', 'last_known_username'], page: $currentPage);
            }
            else {
                $users = User::whereIn('id', $request->users)
                    ->paginate(1000, ['id', 'last_known_username'], page: $currentPage);
            }

            $users->each(function (User $user) use (&$messages, $now, &$request, &$sentToUsers, &$claimable) {
                $messages[] = [
                    'received' => $now->toDateTimeString(),
                    'user_id' => $user->id,
                    'title' => $request->title,
                    'body' => $request->body,
                    'flag' => 'NEW',
                    'tag' => $request->tag,
                    'expire_at' => $request->expireAt?->toDateTimeString(),
                    'claimable' => json_encode($claimable),
                ];
                $sentToUsers[] = $user->last_known_username.' ('.$user->id.')';
            });

            InboxMessage::insert($messages);

        }while($users->lastPage() > $currentPage);

        $this->messageLog($request, $sentToUsers);

        \Session::flash('alert-success', count($sentToUsers).' Messages have been successfully sent.');
        return back()->withInput();
    }

    private function messageLog(MailerSendRequest &$request, array $usernames)
    {
        $user = \Auth::user();
        Log::channel('inbox_traffic')->info(json_encode([
                'sender' => $user->last_known_username.' ('.$user->id.')',
                'messageInput' => [
                    'title' => $request->title,
                    'body' => $request->body,
                    'tag' => $request->tag,
                    'expire_at' => $request->expireAt,
                    'rewards' => $request->rewards,
                ],
                'number_of_users' => count($usernames),
                'sendToUsers' => $usernames,
            ],
            JSON_PRETTY_PRINT)
        );
    }

}
