@php
    /** @var \App\Models\Admin\BadChatMessage $message */
@endphp
<form action="{{ route('chat-filter.handle', ['message' => $message->id]) }}" method="post" id="{{ $message->id }}form">
    @csrf
    <tr>
        <td class="px-8">
            <div class="col-start-1 flex gap-2 justify-center items-center">
                <img src="{{ $message->user->avatar_small }}"
                     alt="Profile Picture of {{ $message->user->last_known_username }}">
                <span>
                {{ $message->user->last_known_username ?? $message->user->id }}
            </span>
            </div>
        </td>
        <td>
            <div class="bg-slate-700 border border-slate-500 p-2 rounded-md">
                {{ $message->message }}
            </div>
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
                <div class="bg-slate-700 border border-slate-500 p-2 rounded-md">
                    {{ $message->consequences ?? '---' }}
                </div>
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
                <x-inputs.button
                        type="button"
                        class="save"
                        href="#{{ $message->id }}handle-modal"
                        rel="modal:open"
                >
                    Handle
                </x-inputs.button>
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
