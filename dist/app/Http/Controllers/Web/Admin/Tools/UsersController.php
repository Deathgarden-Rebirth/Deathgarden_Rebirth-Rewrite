<?php

namespace App\Http\Controllers\Web\Admin\Tools;

use App\APIClients\HttpMethod;
use App\Attributes\IgnorePermissionCheck;
use App\Enums\Auth\Permissions;
use App\Enums\Game\Characters;
use App\Enums\Game\Runner;
use App\Http\Requests\Api\Admin\Tools\BanPostRequest;
use App\Http\Requests\Api\Admin\Tools\InboxMessagePostRequest;
use App\Http\Requests\Api\Admin\UserDetails\EditUserRequest;
use App\Models\Game\Inbox\InboxMessage;
use App\Models\User\Ban;
use App\Models\User\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class UsersController extends AdminToolController
{
    protected static string $name = 'Users';

    protected static string $description = 'View & Manage Users';

    protected static string $iconComponent = 'icons.users';

    protected static Permissions $neededPermission = Permissions::VIEW_USERS;

    const PER_PAGE = 15;

    const USER_DROPDOWN_PER_PAGE = 20;

    public function index(Request $request)
    {
        $searchString = $request->input('search');
        $query = User::orderBy('last_known_username');

        if($searchString !== null) {
            $query->orWhere('id', 'LIKE', '%'.$searchString.'%')
                ->orWhere('steam_id', 'LIKE', '%'.$searchString.'%')
                ->orWhere('last_known_username', 'LIKE', '%'.$searchString.'%');
        }

        $users = $query->paginate(static::PER_PAGE);

        return view('admin.tools.user-list', ['userList' => $users, 'searchString' => $searchString]);
    }

    public function details(User $user)
    {
        if(!Auth::user()->can(Permissions::VIEW_USERS->value))
            abort(403, 'No Permission to view this Page');

        $this->overrideTitle('User Details: '.$user->last_known_username);

        return view('admin.tools.user-details', [
            'user' => $user,
        ]);
    }

    public function edit(User $user, EditUserRequest $request)
    {
        if(!Auth::user()->can(Permissions::EDIT_USERS->value))
            abort(403);

        $playerData = $user->playerData();

        $playerData->currency_a = $request->currencyA;
        $playerData->currency_b = $request->currencyB;
        $playerData->currency_c = $request->currencyC;

        $playerData->last_faction = $request->lastFaction;
        $playerData->last_runner = $request->lastRunner;
        $playerData->last_hunter = $request->lastHunter;

        $playerData->has_played_dg_1 = $request->hasPlayedDG1;
        $playerData->hunter_faction_level = $request->hunterFactionLevel;
        $playerData->runner_faction_level = $request->runnerFactionLevel;

        $playerData->save();

        foreach (Characters::cases() as $character) {
            $characterData = $playerData->characterDataForCharacter($character);
            $characterData->level = $request->{'level'.$character->value};
            $characterData->save();
        }

        Session::flash('alert-success', 'Edits Saved Successfully!');

        return redirect()->back();
    }

    #[IgnorePermissionCheck]
    public function getUsersForDropdown(Request $request)
    {
        // only allow the retrieval of the dropdown if we have the view users permission or inbox mailer because there we need it.
        if(!Auth::check() || !(Auth::user()->can(Permissions::VIEW_USERS->value) || Auth::user()->can(Permissions::INBOX_MAILER->value)))
            abort(403);

        $searchTerm = $request->input('term');

        if($searchTerm === null)
            abort(400, 'Search term must be provided.');

        $query = User::orderBy('last_known_username')
            ->orWhere('id', 'LIKE', '%'.$searchTerm.'%')
            ->orWhere('steam_id', 'LIKE', '%'.$searchTerm.'%')
            ->orWhere('last_known_username', 'LIKE', '%'.$searchTerm.'%')
            ->select(['id', 'last_known_username']);

        /** @var Collection|LengthAwarePaginator $users */
        $users = $query->paginate(static::USER_DROPDOWN_PER_PAGE);

        $result = [];
        $users->each(function ($user) use (&$result) {
            $result[] = [
                'id' => $user->id,
                'text' => $user->last_known_username.' ('.$user->id.')',
            ];
        });

        return [
            'results' => $result,
            'pagination' => [
                'more' => $users->currentPage() < $users->lastPage(),
            ],
        ];
    }

    public function reset(User $user)
    {
        if(!Auth::user()->can(Permissions::EDIT_USERS->value))
            abort(403);

        $user->playerData()->delete();
        return redirect()->back();
    }

    public function bans(User $user)
    {
        if(!Auth::user()->can(Permissions::USER_BANS->value))
            abort(403);

        $bans = $user->bans;
        $this->overrideTitle('Bans for User: '.$user->id.'('.$user->last_known_username.')');

        return view('admin.tools.user-bans', [
            'user' => $user,
            'bans' => $bans,
        ]);
    }

    public function banPost(User $user, Ban $ban, BanPostRequest $request) {
        if($request->editMethod === HttpMethod::DELETE) {
            $ban->delete();
            Session::flash('alert-success', 'Ban Deleted Successfully!');
            return back();
        }

        $ban->ban_reason = $request->reason;
        $ban->start_date = Carbon::parse($request->startDate, 'Europe/Berlin');
        $ban->end_date = Carbon::parse($request->endDate, 'Europe/Berlin');
        $ban->save();

        Session::flash('alert-success', 'Ban Edited Successfully!');

        return back();
    }

    public function createBan(User $user) {
        if(!Auth::user()->can(Permissions::USER_BANS->value))
            abort(403);

        $newBan = new Ban();
        $newBan->ban_reason = 'Placeholder';
        $newBan->start_date = Carbon::now()->addWeek();
        $newBan->end_date = Carbon::now()->addWeeks(2);
        $newBan->user()->associate($user);

        $newBan->save();

        Session::flash('alert-success', 'Ban Created Successfully!');

        return back();
    }

    public function inbox(User $user)
    {
        if(!Auth::user()->can(Permissions::VIEW_USERS->value))
            abort(403, 'You are not allowed to view the inbox of users.');

        $allowEdit = Auth::user()->can(Permissions::EDIT_USERS->value);

        /** @var InboxMessage[]|Collection $messages */
        $messages = $user->inboxMessages()->withTrashed()->get();

        $this->overrideTitle('Inbox for User: '.$user->last_known_username);
        return view('admin.tools.user-inbox', [
            'messages' => $messages,
            'allowEdit' => $allowEdit,
            'user' => $user,
        ]);
    }

    public function inboxMessagePost(InboxMessagePostRequest $request, User $user, InboxMessage $message)
    {
        switch ($request->submitAction) {
            case HttpMethod::DELETE:
                $message->delete();
                Session::flash('alert-success', 'Message Deleted Successfully!');
                break;
            case HttpMethod::PUT:
                $message->title = $request->title;
                $message->body = $request->body;
                $message->flag = $request->flag;
                $message->tag = $request->tag;
                $message->expire_at = $request->expireAt;
                $message->setClaimables($request->rewards);
                $message->save();
                Session::flash('alert-success', 'Message successfully saved!');
                break;
        }

        return back();
    }
}
