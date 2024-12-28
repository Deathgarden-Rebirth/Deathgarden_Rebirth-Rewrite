<?php

namespace App\Http\Controllers\Web\Homepage;

use App\Enums\Launcher\FileAction;
use App\Enums\Launcher\Patchline;
use App\Http\Controllers\Controller;
use App\Models\Admin\Versioning\LauncherVersion;
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
        CompView::share('metaDescription', 'After five years, the game has been brought back to life by the
            community and is now playable again, featuring new balance changes for an improved
            experience.');
        CompView::share('metaKeywords', ['Deathgarden', 'Rebirth']);
        return view('web.home');
    }

    public function download(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        $files = GameFile::wherePatchline(Patchline::LIVE)
            ->whereAction(FileAction::ADD)
            ->get();

        static::setTitle('Deathgarden: Rebirth | Download');
        CompView::share('metaKeywords', ['Deathgarden', 'Rebirth', 'Download', 'Launcher']);
        CompView::share('metaDescription', 'Download the official Deathgarden: Rebirth launcher to easily install and update the Deathgarden: Rebirth mod.');
        return view('web.download', ['files' => $files]);
    }

    public function downloadLauncher()
    {
        // Put launcher .exe into storage/app folder
        if(Storage::disk('local')->exists('Deathgarden Launcher_'.LauncherVersion::get()?->launcherVersion.'.exe'))
            return Response::download(Storage::disk('local')->path('Deathgarden Launcher_'.LauncherVersion::get()?->launcherVersion.'.exe'));

        abort(404);
    }

    public function patchNotes(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        static::setTitle('Deathgarden: Rebirth | Patch Notes');
        CompView::share('metaKeywords', ['Deathgarden', 'Rebirth', 'patch', 'patchnnotes', 'patch notes']);
        CompView::share('metaDescription', 'Stay up to date with all the changes, improvements, and updates in Deathgarden: Rebirth. Below is a complete archive of every patch since the mods release.');
        return view('web.patch-notes');
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
