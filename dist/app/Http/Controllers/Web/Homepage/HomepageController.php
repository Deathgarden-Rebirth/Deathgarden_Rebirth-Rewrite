<?php

namespace App\Http\Controllers\Web\Homepage;

use App\Enums\Launcher\FileAction;
use App\Enums\Launcher\Patchline;
use App\Http\Controllers\Controller;
use App\Models\GameFile;
use App\Models\User\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View as CompView;

class HomepageController extends Controller
{
    public function index(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('web.home');
    }

    public function download(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        $files = GameFile::wherePatchline(Patchline::LIVE)
            ->whereAction(FileAction::ADD)
            ->get();

        static::setTitle('Deathgarden: Rebirth | Download');
        return view('web.download', ['files' => $files]);
    }

    public function downloadLauncher()
    {
        // Put launcher .exe into storage/app folder
        if(Storage::disk('local')->exists('launcher.exe'))
            return Response::download(Storage::disk('local')->path('launcher.exe'));

        abort(404);
    }

    public function howToPlay(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        static::setTitle('Deathgarden: Rebirth | How to Play');
        return view('web.how-to-play');
    }

    public function knownIssues(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        static::setTitle('Deathgarden: Rebirth | Known Issues');
        return view('web.known-issues');
    }

    public function eula(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        static::setTitle('Deathgarden: Rebirth | End User License Agreement');
        return view('web.eula');
    }

    public function credits(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        static::setTitle('Deathgarden: Rebirth | Credits');
        return view('web.credits');
    }

    protected static function setTitle(string $title)
    {
        CompView::share('title', $title);
    }
}
