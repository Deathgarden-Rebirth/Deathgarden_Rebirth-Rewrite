<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    const ROUTE_LOGIN = 'login';

    const ROUTE_CALLBACK = 'callback';

    public function callback()
    {
        $user = Socialite::driver('steam');

        dd($user->user());
    }
}
