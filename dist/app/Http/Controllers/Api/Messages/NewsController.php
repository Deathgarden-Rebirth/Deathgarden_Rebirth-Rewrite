<?php

namespace App\Http\Controllers\Api\Messages;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Messages\GameNewsMessagesRequest;
use App\Http\Responses\Api\Messages\News\GameNews;
use App\Http\Responses\Api\Messages\News\GameNewsResponse;
use App\Http\Responses\Api\Messages\News\GameNewsTranslation;
use App\Models\Game\Messages\News;
use Illuminate\Database\Eloquent\Builder;

class NewsController extends Controller
{
    public function getGameNews(GameNewsMessagesRequest $request)
    {
        $newsCollection = News::where('message_type', '=', $request->messageType->value)
            ->where(function (Builder $query) use ($request) {
                $query->where('max_player_level', '>=', $request->playerLevel)
                    ->orWhereNull('max_player_level');
            })
            ->orderBy('created_at', $request->sortDescending ? 'desc' : 'asc')
            ->get();

        $response = new GameNewsResponse();

        foreach ($newsCollection as $news) {
            $newsResponse = new GameNews(
                $news->uuid,
                $news->message_type
            );

            $newsResponse->isOneTimeGameNews = $news->one_time_news;
            $newsResponse->shouldQuitTheGame = $news->should_quit_game;
            $newsResponse->onlyForPlayersThatCompletedAtLeastOneMatch = $news->one_match;
            $newsResponse->redirectMode = $news->redirect_mode;
            $newsResponse->redirectItem = $news->redirect_item;
            $newsResponse->redirectUrl = $news->redirect_url;
            $newsResponse->embeddedBackgroundImage = $news->background_image ?? 'null';
            $newsResponse->embeddedInGameNewsBackgroundImage = $news->in_game_news_background_image ?? 'null';
            $newsResponse->embeddedInGameNewsThumbnailImage = $news->in_game_news_thumbnail ?? 'null';
            $newsResponse->fromDate = $news->from_date;
            $newsResponse->toDate = $news->to_date;
            $newsResponse->displayXTimes = $news->display_x_times;
            $newsResponse->maxPlayerLevel = $news->max_player_level;

            $newsResponse->translations[] = new GameNewsTranslation(
                'EN',
                $news->title,
                $news->body,
            );

            $response->messages[] = $newsResponse;
        }

        return json_encode($response);
    }
}
