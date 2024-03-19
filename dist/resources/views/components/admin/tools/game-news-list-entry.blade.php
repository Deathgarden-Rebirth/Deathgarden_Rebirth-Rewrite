@php
    use App\Enums\Game\Message\MessageType;
    use App\Models\Game\Messages\News;
    use App\Enums\Game\Faction;
    use App\Enums\Game\Message\GameNewsRedirectMode;
    /** @var News $news */
    /** @var string $idPrefix */
@endphp

@pushonce('head')
    @vite(['resources/css/components/admin/tools/game-news-list-entry.scss'])
@endpushonce

<div class="container mx-auto bg-slate-800 border border-slate-500 rounded-xl my-6 p-2 shadow-glow shadow-gray-400/30">
    <form action="{{ route('gamenews.post', ['newsId' => $news->uuid]) }}" method="post">
        @csrf
        <div class="game-news-entry-attributes">
            <div>
                <label for="title">Title</label>
                <x-inputs.text value="{{ $news->title }}"/>
            </div>
            <div>
                <label for="description">Description</label>
                <x-inputs.text-area id="{{ $idPrefix }}description">{{ $news->body }}</x-inputs.text-area>
            </div>
            <div>
                <label for="messageType">Message Type</label>
                <x-inputs.dropdown
                        id="messageType"
                        :cases="MessageType::cases()"
                        :selected="$news->message_type"/>
            </div>
            <div>
                <label for="faction">Faction</label>
                <x-inputs.dropdown
                        id="faction"
                        :cases="Faction::cases()"
                        :selected="$news->faction"/>
            </div>
            <div>
                <label for="oneTime">One Time News</label>
                <x-inputs.checkbox :checked="$news->one_time_news"/>
            </div>
            <div>
                <label for="oneTime">Should Quit Game</label>
                <x-inputs.checkbox id="oneTime" :checked="$news->should_quit_game"/>
            </div>
            <div>
                <label for="oneMatch">Complete One Match</label>
                <x-inputs.checkbox id="oneMatch" :checked="$news->one_match"/>
            </div>
            <div>
                <label for="redirectMode">Redirect Mode</label>
                <x-inputs.dropdown
                        id="faction"
                        :cases="GameNewsRedirectMode::cases()"
                        :selected="$news->redirect_mode"/>
            </div>
            <div>
                <label for="redirectItem">Redirect Item</label>
                <x-inputs.text id="redirectItem" value="{{ $news->redirect_item }}"/>
            </div>
            <div>
                <label for="redirectUrl">Redirect Url</label>
                <x-inputs.text id="redirectUrl" value="{{ $news->redirect_url }}"/>
            </div>
            <div>
                <label for="PopUpBG">Pop-Up News Background</label>
                <x-inputs.text id="PopUpBG" value="{{ $news->background_image }}"/>
            </div>
            <div>
                <label for="inGameNewsBG">In Game News Background</label>
                <x-inputs.text id="inGameNewsBG" value="{{ $news->background_image }}"/>
            </div>
            <div>
                <label for="inGameNewsThumbnail">In Game News Thumbnail</label>
                <x-inputs.text id="inGameNewsThumbnail" value="{{ $news->background_image }}"/>
            </div>
            <div>
                <label for="fromDate">From Date</label>
                <x-inputs.date id="fromDate" value="{{ $news->from_date->toDateString() }}"/>
            </div>
            <div>
                <label for="toDate">To Date</label>
                <x-inputs.date id="toDate" value="{{ $news->to_date->toDateString() }}"/>
            </div>
            <div>
                <label for="displayXTimes">Display X Times</label>
                <x-inputs.number id="displayXTimes" min="0" value="{{ $news->display_x_times }}"/>
            </div>
            <div>
                <label for="maxPlayerLevel">Max Player Level</label>
                <x-inputs.number id="maxPlayerLevel" min="1" value="{{ $news->max_player_level }}"/>
            </div>
        </div>
        <div class="flex justify-start gap-6 mt-2">
            <x-inputs.submit>Stuff</x-inputs.submit>
            <x-inputs.submit class="save">Save</x-inputs.submit>
            <x-inputs.submit class="delete">Delete</x-inputs.submit>
            <x-inputs.submit class="create">Create</x-inputs.submit>
        </div>
    </form>
</div>

