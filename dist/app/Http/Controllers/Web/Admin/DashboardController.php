<?php

namespace App\Http\Controllers\Web\Admin;

use App\Enums\Auth\Permissions;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\View;

class DashboardController extends Controller
{
    public function dashboard()
    {
        Artisan::call('permission:cache-reset');
        if(Auth::user()->cant(Permissions::ADMIN_AREA->value))
            throw new AuthorizationException();

        View::share('title', 'Dashboard');
        return view('admin.dashboard');
    }
}
