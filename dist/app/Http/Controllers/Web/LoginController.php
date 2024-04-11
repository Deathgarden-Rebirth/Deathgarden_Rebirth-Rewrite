<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Laravel\Socialite\Facades\Socialite;
use SocialiteProviders\Steam\Provider as SteamProvider;

class LoginController extends Controller
{
    const ROUTE_LOGIN = 'login';
    
    const ROUTE_CALLBACK = 'callback';
    
    public function redirect(Request $request)
    {
        $launcher = $request->input('launcher');
        $request->session()->put('launcher_port', $request->input('port'));

        $steamServiceConfig = Config::get('services.steam');

        if ($launcher === "yes") {
            $steamServiceConfig['redirect'] = $steamServiceConfig['redirect_launcher'];
        }

        return Socialite::buildProvider(SteamProvider::class, $steamServiceConfig)->redirect();
    }

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

    public function launcherCallback() : RedirectResponse
    {
        $user = Socialite::driver('steam')->user();
        $loggedInUser = User::firstOrCreate(['steam_id' => $user->getId()], ['source' => 'WEB']);
        Auth::login($loggedInUser);
        $loggedInUser->last_known_username = $user->getNickname();
        $loggedInUser->avatar_small = $user->user['avatar'];
        $loggedInUser->avatar_medium = $user->getAvatar();
        $loggedInUser->avatar_full = $user->user['avatarfull'];

        $loggedInUser->save();

        Log::stack(['stack', 'login'])->info('User with SteamID "{id}" successfully logged in via Launcher.', ['id' => $loggedInUser->steam_id]);

        return Redirect::away('http://localhost:'. request()->session()->get('launcher_port') .'/auth');
    }
}
