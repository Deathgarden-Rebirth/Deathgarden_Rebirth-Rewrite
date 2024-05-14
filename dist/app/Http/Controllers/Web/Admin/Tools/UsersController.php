<?php

namespace App\Http\Controllers\Web\Admin\Tools;

use App\APIClients\HttpMethod;
use App\Enums\Auth\Permissions;
use App\Enums\Game\Characters;
use App\Enums\Game\Runner;
use App\Http\Requests\Api\Admin\Tools\BanPostRequest;
use App\Http\Requests\Api\Admin\UserDetails\EditUserRequest;
use App\Models\User\Ban;
use App\Models\User\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class UsersController extends AdminToolController
{
    protected static string $name = 'Users';

    protected static string $description = 'View and Manage Users';

    protected static string $iconComponent = 'icons.users';

    protected static Permissions $neededPermission = Permissions::VIEW_USERS;

    const PER_PAGE = 15;

    public function index(Request $request)
    {
        if(!Auth::user()->can(Permissions::VIEW_USERS->value))
            abort(403, 'No Permission to view this Page');

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

        View::share('title', 'User Details: '.$user->last_known_username);

        return view('admin.tools.user-details', [
            'user' => $user,
        ]);
    }

    public function edit(User $user, EditUserRequest $request)
    {
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

    public function reset(User $user)
    {
        $canReset = Auth::check() && Auth::user()->can(Permissions::EDIT_USERS->value);

        if(!$canReset)
            abort(403, 'You dont have enough permissions for this action.');

        $user->playerData()->delete();
        return redirect()->back();
    }

    public function bans(User $user)
    {
        $bans = $user->bans;
        View::share('title', 'Bans for User: '.$user->id.'('.$user->last_known_username.')');

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
        $ban->start_date = $request->startDate;
        $ban->end_date = $request->endDate;
        $ban->save();

        Session::flash('alert-success', 'Ban Edited Successfully!');

        return back();
    }

    public function createBan(User $user) {
        $newBan = new Ban();
        $newBan->ban_reason = 'Placeholder';
        $newBan->start_date = Carbon::now()->addWeek();
        $newBan->end_date = Carbon::now()->addWeeks(2);
        $newBan->user()->associate($user);

        $newBan->save();

        Session::flash('alert-success', 'Ban Created Successfully!');

        return back();
    }
}
