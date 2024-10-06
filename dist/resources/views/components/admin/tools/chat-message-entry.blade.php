@php
    use App\Models\Admin\Archive\ArchivedGame;
    /** @var \App\Models\Admin\BadChatMessage $message */

    $match = ArchivedGame::find($message->match_id);
@endphp

<form action="{{ route('chat-filter.handle', ['message' => $message->id]) }}" method="post" id="{{ $message->id }}form">
    @csrf
    <tr>
        <td class="px-8">
            {{ $message->created_at }}
        </td>
        <td class="px-8">
            <a href="{{ route('user.details', ['user' => $message->hostUser->id]) }}" target="_blank">
                <div class="col-start-1 flex gap-2 justify-center items-center">
                    <img src="{{ $message->hostUser->avatar_small }}"
                         alt="Profile Picture of {{ $message->hostUser->last_known_username }}">
                    <span>
                {{ $message->hostUser->last_known_username ?? $message->hostUser->id }}
            </span>
                </div>
            </a>
        </td>
        <td class="px-8">
            <a href="{{ route('user.details', ['user' => $message->user->id]) }}" target="_blank">
                <div class="col-start-1 flex gap-2 justify-center items-center">
                    <img src="{{ $message->user->avatar_small }}"
                         alt="Profile Picture of {{ $message->user->last_known_username }}">
                    <span>
                {{ $message->user->last_known_username ?? $message->user->id }}
            </span>
                </div>
            </a>
        </td>
        <td>
            <div class="bg-slate-700 border border-slate-500 p-2 rounded-md">
                {{ $message->message }}
            </div>
            @if($match !== null)
                <x-inputs.button
                        type="button"
                        href="#{{ $message->match_id }}-chat"
                        rel="modal:open"
                >
                    @if($match === null)
                        not found :(
                    @else
                        match chat
                    @endif
                </x-inputs.button>
                <div id="{{ $match->id }}-chat" class="modal">
                    <div class="flex flex-col items-center gap-5">
            <span>
                {{ json_encode($match->chat_messages) }}
            </span>
                    </div>
                </div>
            @endif
        </td>
        <td>
            @if($message->handled)
                <div class="flex justify-center items-center">
                    <x-icons.checkmark class="size-6 bg-green-500 rounded-md"/>
                </div>
            @else
                <div class="flex justify-center items-center">
                    <div class="bg-red-500 rounded-md">
                        <x-icons.plus class="size-6 rotate-45"/>
                    </div>
                </div>
            @endif
        </td>
        @if($message->handled)
            <td>
                <div class="bg-slate-700 border border-slate-500 p-2 rounded-md whitespace-pre">{{ $message->consequences ?? '---' }}</div>
            </td>
            <td>
                @if($message->handledBy === null)
                    Handler Unknown
                @else
                    <div class="col-start-1 flex gap-2 justify-center items-center">
                        <img src="{{ $message->handledBy?->avatar_small }}"
                             alt="Profile Picture of {{ $message->handledBy?->last_known_username }}">
                        <span>
                        {{ $message->handledBy?->last_known_username ?? $message->handledBy?->id }}
                    </span>
                    </div>
                @endif
            </td>
        @endif
        @if(!$message->handled)
            <td>
                <div class="flex items-center justify-center gap-2">
                    <x-inputs.button
                            type="button"
                            class="save"
                            href="#{{ $message->id }}handle-modal"
                            rel="modal:open"
                    >
                        <span class="size-6">
                            Handle
                        </span>
                    </x-inputs.button>
                    <a href="{{ route('user.bans', ['user' => $message->user->id]) }}" target="_blank">
                        <x-inputs.button type="button" class="px-2 py-1 !text-sm delete" title="Bans">
                            <x-icons.hammer class="size-6"/>
                        </x-inputs.button>
                    </a>
                    <a href="{{ route('user.inbox', ['user' => $message->user->id]) }}" target="_blank">
                        <x-inputs.button type="button" class="px-2 py-1 !text-sm" title="Inbox">
                            <x-icons.mail class="size-6"/>
                        </x-inputs.button>
                    </a>
                </div>

                <div id="{{ $message->id }}handle-modal" class="modal">
                    <div class="flex flex-col items-center gap-5">
                        <label for="{{ $message->id }}consequences">
                            Consequences
                        </label>
                        <x-inputs.text-area
                                form="{{ $message->id }}form"
                                id="{{ $message->id }}consequences"
                                class="!w-full"
                                name="consequences"
                        />

                        <div class="flex gap-5">
                            <x-inputs.button type="button" href="#close" rel="modal:close">
                                Cancel
                            </x-inputs.button>
                            <x-inputs.button
                                    form="{{ $message->id }}form"
                                    class="create"
                                    formnovalidate
                            >
                                Mark as Handled
                            </x-inputs.button>
                        </div>
                    </div>
                </div>
            </td>
        @endif
    </tr>
</form>


