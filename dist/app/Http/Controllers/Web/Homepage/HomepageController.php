<?php

namespace App\Http\Controllers\Web\Homepage;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\View as CompView;

class HomepageController extends Controller
{
    public function index(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('web.home');
    }

    public function download(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        static::setTitle('Deathgarden Rebirth | Download');
        return view('web.download');
    }

    public function howToPlay(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        static::setTitle('Deathgarden Rebirth | How to Play');
        return view('web.how-to-play');
    }

    public function eula(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        static::setTitle('Deathgarden Rebirth | End User License Agreement');
        return view('web.eula');
    }

    protected static function setTitle(string $title)
    {
        CompView::share('title', $title);
    }
}
