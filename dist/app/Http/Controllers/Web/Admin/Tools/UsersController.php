<?php

namespace App\Http\Controllers\Web\Admin\Tools;

use App\Enums\Auth\Permissions;
use App\Models\User\User;
use Illuminate\Http\Request;
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
        View::share('title', 'User Details: '.$user->last_known_username);

        return view('admin.tools.user-details', [
            'user' => $user,
        ]);
    }
}
