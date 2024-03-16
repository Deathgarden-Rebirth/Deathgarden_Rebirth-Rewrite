<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    const ROUTE_LOGIN = 'login';

    const ROUTE_CALLBACK = 'callback';

    public function callback()
    {
        $user = Socialite::driver('steam')->user();
        $loggedInUser = User::firstOrCreate(['steam_id' => $user->getId()], ['source' => 'WEB']);
        Auth::login($loggedInUser);
        $loggedInUser->last_known_username = $user->getNickname();
        $loggedInUser->avatar_small = $user->user['avatar'];
        $loggedInUser->avatar_medium = $user->getAvatar();
        $loggedInUser->avatar_full = $user->user['avatarfull'];

        $loggedInUser->save();

        Log::stack(['stack', 'login'])->info('User with SteamID "{id}" successfully logged in via Web.', ['id' => $loggedInUser->steam_id]);

        return Redirect::intended();
    }
}
