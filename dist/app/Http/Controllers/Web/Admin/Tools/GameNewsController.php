<?php

namespace App\Http\Controllers\Web\Admin\Tools;

use App\APIClients\HttpMethod;
use App\Enums\Auth\Permissions;
use App\Enums\Game\Faction;
use App\Enums\Game\Message\GameNewsRedirectMode;
use App\Enums\Game\Message\MessageType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\Tools\SubmitGameNewsRequest;
use App\Models\Game\Messages\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class GameNewsController extends AdminToolController
{
    protected static string $name = 'Game News';

    protected static string $description = 'Create and edit in game news.';

    protected static string $iconComponent = 'icons.gamenews';

    protected static Permissions $neededPermission = Permissions::GAME_NEWS;

    public function index()
    {
        $news = News::orderByDesc('created_at')->get();

        return view('admin.tools.game-news', ['newsList' => $news]);
    }

    public function create()
    {
        News::create();

        return Redirect::back();
    }

    public function submit(News $news, SubmitGameNewsRequest $request) {
        switch(HttpMethod::tryFrom($request->input(SubmitGameNewsRequest::SUBMIT_METHOD))) {
            case HttpMethod::PUT:
                $this->updateNews($news, $request);
                break;
            case HttpMethod::DELETE:
                $this->deleteNews($news, $request);
                break;
            default:
        }

        return Redirect::back();
    }

    protected function updateNews(News &$news, SubmitGameNewsRequest &$request)
    {
        $news->enabled = $request->input(SubmitGameNewsRequest::ENABLED) !== null;
        $news->title = $request->input(SubmitGameNewsRequest::TITLE);
        $news->body = $request->input(SubmitGameNewsRequest::DESCRIPTION);

        $news->message_type = MessageType::tryFrom($request->input(SubmitGameNewsRequest::MESSSAGE_TYPE));
        $news->faction = Faction::tryFrom($request->input(SubmitGameNewsRequest::FACTION, Faction::None->value));
        $news->one_time_news = $request->input(SubmitGameNewsRequest::ONE_TIME_NEWS) !== null;
        $news->should_quit_game = $request->input(SubmitGameNewsRequest::QUIT_GAME) !== null;
        $news->one_match = $request->input(SubmitGameNewsRequest::COMPLETE_ONE_MATCH) !== null;

        $news->redirect_mode = GameNewsRedirectMode::tryFrom($request->input(SubmitGameNewsRequest::REDIRECT_MODE, GameNewsRedirectMode::None->value));
        $news->redirect_item = $request->input(SubmitGameNewsRequest::REDIRECT_ITEM);
        $news->redirect_url = $request->input(SubmitGameNewsRequest::REDIRECT_URL);

        $news->background_image = $request->input(SubmitGameNewsRequest::POP_UP_BACKGROUND);
        $news->in_game_news_background_image = $request->input(SubmitGameNewsRequest::IN_GAME_BACKGROUND);
        $news->in_game_news_thumbnail = $request->input(SubmitGameNewsRequest::IN_GAME_THUMBNAIL);

        $news->from_date = $request->input(SubmitGameNewsRequest::FROM_DATE);
        $news->to_date = $request->input(SubmitGameNewsRequest::TO_DATE);
        $news->display_x_times = $request->input(SubmitGameNewsRequest::DISPLAY_X_TIMES);
        $news->max_player_level = $request->input(SubmitGameNewsRequest::MAX_PLAYER_LEVEL);

        $result = $news->save();

        if($result)
            Session::flash('alert-success', 'Successfully updated News "'.$news->title.'".');

        return Redirect::back();
    }

    protected function deleteNews(News &$news, SubmitGameNewsRequest &$request)
    {

    }

}
