<?php

namespace App\Http\Controllers\Web\Homepage;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class HomepageController extends Controller
{
    public function index(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('web.home');
    }

    public function download(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('web.download');
    }

    public function howToPlay(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('web.how-to-play');
    }
}
