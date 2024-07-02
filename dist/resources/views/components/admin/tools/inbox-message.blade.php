@php
    /** @var \App\Models\Game\Inbox\InboxMessage $message */
    /** @var bool $allowEdit */
@endphp

<div class="container p-4 bg-slate-800 border-slate-500 border rounded-xl">
    <h1 class="float-right right-0 flex justify-end text-slate-500">
        Database-ID: {{ $message->id }}
    </h1>
    <form action="{{ route('user.inboxMessage.edit', ['user' => $message->user_id, 'message' => $message->id]) }}" method="post" id="{{ $idPrefix }}inbpx-message-form">
        @csrf
        <div class="inbox-entry-attributes">
            <div class="section flex-col">
                <h1>Content</h1>
                <div>
                    <label for="{{ $message->id }}_title">
                        Title
                    </label>
                    @if($allowEdit)
                        <x-inputs.text
                                id="{{ $message->id }}_title"
                                name="title"
                                required
                                value="{{ $message->title }}"
                        />
                    @else
                        <div class="w-full bg-slate-700 p-2 border border-slate-600 rounded-md">
                            {{ $message->title }}
                        </div>
                    @endif
                </div>

                <div>
                    <label for="{{ $message->id }}_body">
                        Body
                    </label>
                    @if($allowEdit)
                        <x-inputs.text-area
                                id="{{ $message->id }}_body"
                                class="h-44"
                                name="body"
                                required>{{ $message->body }}</x-inputs.text-area>
                    @else
                        <div class="w-full bg-slate-700 p-2 border border-slate-600 rounded-md whitespace-pre">{{ $message->body }}</div>
                    @endif
                </div>
                <div class="section columns-1 lg:columns-2 xl:columns-3">
                    <h1>Metadata</h1>
                    <div>
                        <label for="{{ $message->id }}_flag">
                            Flag
                        </label>
                        @if($allowEdit)
                            <x-inputs.dropdown
                                    id="{{ $message->id }}_flag"
                                    name="flag"
                                    required
                                    :cases="['NEW', 'READ']"
                                    :selected="$message->flag"/>
                        @else
                            <div class="w-full bg-slate-700 p-2 border border-slate-600 rounded-md whitespace-pre">{{ $message->flag }}</div>
                        @endif
                    </div>

                    <div>
                        <label for="{{ $message->id }}_tag">
                            Tag
                        </label>
                        @if($allowEdit)
                            <x-inputs.text
                                    id="{{ $message->id }}_tag"
                                    name="tag"
                                    required
                                    value="{{ $message->tag }}"
                            />
                        @else
                            <div class="w-full bg-slate-700 p-2 border border-slate-600 rounded-md">
                                {{ $message->tag }}
                            </div>
                        @endif
                    </div>

                    <div>
                        <label for="{{ $message->id }}_tag">
                            Expire At
                        </label>
                        @if($allowEdit)
                            <x-inputs.date
                                    type="datetime-local"
                                    id="{{ $message->id }}_expireAt"
                                    name="expireAt"
                                    value="{{ $message->expire_at->toDateTimeString('minute') }}"
                            />
                        @else
                            <div class="w-full bg-slate-700 p-2 border border-slate-600 rounded-md">
                                {{ $message->expire_at }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="section">
                    <h1>Rewards</h1>
                    <x-admin.tools.item-selector :rewards="$message->getClaimables()" />
                </div>
                <div class="mt-8 flex gap-8">
                    <x-inputs.button
                            class="save"
                            name="submitAction"
                            value="{{ \App\APIClients\HttpMethod::PUT }}"
                    >Save</x-inputs.button>
                    <x-inputs.button
                            href="#{{ $idPrefix }}message-delete-modal"
                            rel="modal:open"
                            type="button"
                            class="delete"
                            name="submitAction"
                            value="{{ \App\APIClients\HttpMethod::DELETE }}"
                    >
                        Delete
                    </x-inputs.button>
                </div>
            </div>
        </div>
    </form>
    <div id="{{ $idPrefix }}message-delete-modal" class="modal">
        <div class="flex flex-col items-center gap-5">
                    <span>
                        Are you sure you want to delete the inbox message
                    <span class="italic m-1 inline dark:bg-slate-700 bg-slate-400 w-fit px-2 rounded">
                        {{ $message->title }}
                    </span>
                        ?
                    </span>
            <div class="flex gap-5">
                <x-inputs.button type="button" href="#close" rel="modal:close">
                    Cancel
                </x-inputs.button>
                <x-inputs.button
                        form="{{ $idPrefix }}inbpx-message-form"
                        class="delete"
                        name="submitAction"
                        value="{{ \App\APIClients\HttpMethod::DELETE }}"
                        formnovalidate
                >
                    Delete
                </x-inputs.button>
            </div>
        </div>
    </div>
</div>
