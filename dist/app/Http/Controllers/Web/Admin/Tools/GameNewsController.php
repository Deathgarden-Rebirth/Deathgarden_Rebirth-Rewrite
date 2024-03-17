<?php

namespace App\Http\Controllers\Web\Admin\Tools;

use App\Enums\Auth\Permissions;
use App\Http\Controllers\Controller;
use App\Models\Game\Messages\News;
use Illuminate\Http\Request;

class GameNewsController extends AdminToolController
{
    protected static string $name = 'Game News';

    protected static string $description = 'Create and edit in game news.';

    protected static string $iconComponent = 'icons.gamenews';

    protected static Permissions $neededPermission = Permissions::GAME_NEWS;

    public function index()
    {
        $news = News::all();

        return view('admin.tools.game-news', ['newsList' => $news]);
    }
}
