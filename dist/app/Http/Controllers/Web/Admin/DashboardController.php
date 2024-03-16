<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class DashboardController extends Controller
{
    public function dashboard()
    {
        View::share('title', 'Dashboard');
        return view('admin.dashboard');
    }
}
