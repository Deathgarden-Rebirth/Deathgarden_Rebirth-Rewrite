@php
    use App\Enums\Game\Message\MessageType;
    use App\Http\Requests\Api\Admin\Tools\SubmitGameNewsRequest;
    use App\Models\Game\Messages\News;
    use App\Enums\Game\Faction;
    use App\Enums\Game\Message\GameNewsRedirectMode;
    /** @var News $news */
    /** @var string $idPrefix */
@endphp

@pushonce('head')
    @vite(['resources/css/components/admin/tools/game-news-list-entry.scss'])
@endpushonce

<div class="container mx-auto bg-slate-300 dark:bg-slate-800 border dark:border-slate-500 rounded-xl my-6 py-2 px-4 shadow-xl dark:shadow-glow dark:shadow-gray-400/30"
     x-data="{
        messageType: '{{ $news->message_type->value}}',
        redirectMode: '{{ $news->redirect_mode->value }}'
    }"
>
    <form action="{{ route('gamenews.post', ['news' => $news->uuid]) }}" method="post" id="{{ $idPrefix }}news-form">
        @csrf
        <div class="float-right mt-[-1rem]">
            <span class="text-white/75">{{ $news->uuid }}</span>
        </div>
        <div class="game-news-entry-attributes">
            <div class="section flex-col">
                <h1>Main Settings</h1>
                <div>
                    <label for="{{ $idPrefix }}enabled">Enabled</label>
                    <x-inputs.checkbox
                            id="{{ $idPrefix }}enabled"
                            name="{{ SubmitGameNewsRequest::ENABLED }}"
                            :checked="$news->enabled"/>
                </div>
                <div>
                    <label for="{{ $idPrefix }}title">Title</label>
                    <x-inputs.text id="{{ $idPrefix }}title"
                                   required
                                   name="{{ SubmitGameNewsRequest::TITLE }}"
                                   value="{{ $news->title }}"/>
                </div>
                <div>
                    <label for="{{ $idPrefix }}description">Description</label>
                    <x-inputs.text-area
                            id="{{ $idPrefix }}description"
                            required
                            name="{{ SubmitGameNewsRequest::DESCRIPTION }}"
                    >{{ $news->body }}</x-inputs.text-area>
                </div>
            </div>
            <div class="section columns-1 lg:columns-2 xl:columns-3">
                <h1>Visibility Settings</h1>
                <div>
                    <label for="{{ $idPrefix }}messageType">Message Type</label>
                    <x-inputs.dropdown
                            id="{{ $idPrefix }}messageType"
                            required
                            name="{{ SubmitGameNewsRequest::MESSSAGE_TYPE }}"
                            x-model="messageType"
                            :cases="MessageType::cases()"
                            :selected="$news->message_type"/>
                </div>
                <div>
                    <label for="{{ $idPrefix }}faction">Faction</label>
                    <x-inputs.dropdown
                            id="{{ $idPrefix }}faction"
                            name="{{ SubmitGameNewsRequest::FACTION }}"
                            x-bind:disabled="messageType === '{{ MessageType::PopUpNews->value }}'"
                            :cases="Faction::cases()"
                            :selected="$news->faction"/>
                </div>
                <div>
                    <label for="{{ $idPrefix }}one-time">One Time News</label>
                    <x-inputs.checkbox
                            id="{{ $idPrefix }}one-time"
                            name="{{ SubmitGameNewsRequest::ONE_TIME_NEWS }}"
                            x-bind:disabled="messageType === '{{ MessageType::InGameNews->value }}'"
                            :checked="$news->one_time_news"/>
                </div>
                <div>
                    <label for="{{ $idPrefix }}quit-game">Should Quit Game</label>
                    <x-inputs.checkbox
                            id="{{ $idPrefix }}quit-game"
                            name="{{ SubmitGameNewsRequest::QUIT_GAME }}"
                            x-bind:disabled="messageType === '{{ MessageType::InGameNews->value }}'"
                            :checked="$news->should_quit_game"/>
                </div>
                <div>
                    <label for="{{ $idPrefix }}one-match">Complete One Match</label>
                    <x-inputs.checkbox
                            id="{{ $idPrefix }}one-match"
                            name="{{ SubmitGameNewsRequest::COMPLETE_ONE_MATCH }}"
                            :checked="$news->one_match"/>
                </div>
            </div>
            <div class="section columns-1 lg:columns-2 xl:columns-3">
                <h1>Redirect</h1>
                <div>
                    <label for="{{ $idPrefix }}redirect-mode">Redirect Mode</label>
                    <x-inputs.dropdown
                            id="{{ $idPrefix }}redirect-mode"
                            required
                            name="{{ SubmitGameNewsRequest::REDIRECT_MODE }}"
                            x-model="redirectMode"
                            x-bind:disabled="messageType === '{{ MessageType::PopUpNews->value }}'"
                            :cases="GameNewsRedirectMode::cases()"
                            :selected="$news->redirect_mode"/>
                </div>
                <div>
                    <label for="{{ $idPrefix }}redirect-item">Redirect Item</label>
                    <x-inputs.text id="{{ $idPrefix }}redirect-item"
                                   name="{{ SubmitGameNewsRequest::REDIRECT_ITEM }}"
                                   x-bind:disabled="
                                   messageType === '{{ MessageType::PopUpNews->value }}' ||
                                   redirectMode === '{{ GameNewsRedirectMode::WebsiteLink->value }}' ||
                                   redirectMode === '{{ GameNewsRedirectMode::None->value }}'"
                                   value="{{ $news->redirect_item }}"/>
                </div>
                <div>
                    <label for="{{ $idPrefix }}redirect-url">Redirect Url</label>
                    <x-inputs.text id="{{ $idPrefix }}redirect-url"
                                   name="{{ SubmitGameNewsRequest::REDIRECT_URL }}"
                                   x-bind:disabled="
                                   messageType === '{{ MessageType::PopUpNews->value }}' ||
                                   redirectMode === '{{ GameNewsRedirectMode::Store->value }}' ||
                                   redirectMode === '{{ GameNewsRedirectMode::None->value }}'"
                                   value="{{ $news->redirect_url }}"/>

                </div>
            </div>
            <div class="section columns-1 lg:columns-2 xl:columns-3">
                <h1>Images</h1>
                <div>
                    <label for="{{ $idPrefix }}pop-up-bg">Pop-Up News Background</label>
                    <x-inputs.asset-dropdown
                            id="{{ $idPrefix }}pop-up-bg"
                            name="{{ SubmitGameNewsRequest::POP_UP_BACKGROUND }}"
                            x-bind:disabled="messageType !== '{{ MessageType::PopUpNews->value }}'"
                            folderPath="news/banners/popup"
                            selected="{{ $news->background_image }}"/>
                </div>
                <div>
                    <label for="{{ $idPrefix }}in-game-news-bg">In Game News Background</label>
                    <x-inputs.asset-dropdown
                            id="{{ $idPrefix }}in-game-news-bg"
                            name="{{ SubmitGameNewsRequest::IN_GAME_BACKGROUND }}"
                            x-bind:disabled="messageType === '{{ MessageType::PopUpNews->value }}'"
                            folderPath="news/banners/inGame_large"
                            selected="{{ $news->in_game_news_background_image }}"/>
                </div>
                <div>
                    <label for="{{ $idPrefix }}in-game-news-thumbnail">In Game News Thumbnail</label>
                    <x-inputs.asset-dropdown
                            id="{{ $idPrefix }}in-game-news-thumbnail"
                            name=" {{ SubmitGameNewsRequest::IN_GAME_THUMBNAIL }}"
                            x-bind:disabled="messageType === '{{ MessageType::PopUpNews->value }}'"
                            folderPath="news/banners/inGame_small"
                            selected="{{ $news->in_game_news_thumbnail }}"/>
                </div>
            </div>
            <div class="section columns-1 lg:columns-2">
                <h1>Metadata</h1>
                <div>
                    <label for="{{ $idPrefix }}from-date">From Date</label>
                    <x-inputs.date
                            id="{{ $idPrefix }}from-date"
                            required
                            name="{{ SubmitGameNewsRequest::FROM_DATE }}"
                            value="{{ $news->from_date?->toDateString() }}"/>
                </div>
                <div>
                    <label for="{{ $idPrefix }}to-date">To Date</label>
                    <x-inputs.date
                            id="{{ $idPrefix }}to-date"
                            required
                            name="{{ SubmitGameNewsRequest::TO_DATE }}"
                            value="{{ $news->to_date?->toDateString() }}"/>
                </div>
                <div>
                    <label for="{{ $idPrefix }}display-x-times">Display X Times</label>
                    <x-inputs.number
                            id="{{ $idPrefix }}display-x-times"
                            name="{{ SubmitGameNewsRequest::DISPLAY_X_TIMES }}"
                            min="0"
                            x-bind:disabled="messageType !== '{{ MessageType::PopUpNews->value }}'"
                            value="{{ $news->display_x_times }}"/>
                </div>
                <div>
                    <label for="{{ $idPrefix }}max-player-level">Max Player Level</label>
                    <x-inputs.number
                            id="{{ $idPrefix }}max-player-level"
                            name="{{ SubmitGameNewsRequest::MAX_PLAYER_LEVEL }}"
                            min="1"
                            x-bind:disabled="messageType === '{{ MessageType::PopUpNews->value }}'"
                            value="{{ $news->max_player_level }}"/>
                </div>
            </div>
        </div>
        <div class="flex justify-start gap-6 mt-4">
            <x-inputs.button
                    class="save"
                    name="{{ SubmitGameNewsRequest::SUBMIT_METHOD }}"
                    value="{{ \App\APIClients\HttpMethod::PUT }}"
            >
                Save
            </x-inputs.button>
            <x-inputs.button
                    href="#{{ $idPrefix }}delete-modal"
                    rel="modal:open"
                    type="button"
                    class="delete"
                    name="{{ SubmitGameNewsRequest::SUBMIT_METHOD }}"
                    value="{{ \App\APIClients\HttpMethod::DELETE }}"
            >
                Delete
            </x-inputs.button>
            <div id="{{ $idPrefix }}delete-modal" class="modal">
                <div class="flex flex-col items-center gap-5">
                    <span>
                        Are you sure you want to delete the news
                    <span class="italic m-1 inline dark:bg-slate-700 bg-slate-400 w-fit px-2 rounded">
                        {{ $news->title }}
                    </span>
                        ?
                    </span>
                    <div class="flex gap-5">
                        <x-inputs.button type="button" href="#close" rel="modal:close">
                            Cancel
                        </x-inputs.button>
                        <x-inputs.button
                                form="{{ $idPrefix }}news-form"
                                class="delete"
                                name="{{ SubmitGameNewsRequest::SUBMIT_METHOD }}"
                                value="{{ \App\APIClients\HttpMethod::DELETE }}"
                                formnovalidate
                        >
                            Delete
                        </x-inputs.button>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>

