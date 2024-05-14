@props([
    'user',
    'bans' => [],
])

@php
    /** @var \App\Models\User\User $user */
    /** @var \App\Models\User\Ban[] $bans */
@endphp

<x-layouts.admin>
    @if(count($bans) <= 0)
        <div class="flex justify-center items-center gap-6 m-12 text-xl font-bold flex-col">
            <span>The User currently has no Bans.</span>
        </div>
    @else
        <div class="w-full p-2 md:px-16 bg-inherit">
            @foreach($bans as $ban)
                <div class="container mx-auto bg-slate-300 dark:bg-slate-800 border dark:border-slate-500 rounded-xl my-6 py-2 px-4 shadow-xl dark:shadow-glow dark:shadow-gray-400/30">
                    <h1 class="flex justify-end p-2 text-slate-500">
                        Database-ID: {{ $ban->id }}
                    </h1>
                    <form action="{{ route('user.ban.post', ['user' => $user->id, 'ban' => $ban->id]) }}" method="post">
                        @csrf
                        <div class="flex justify-center items-center gap-4">
                            <label for="{{ $ban->id }}_reason">
                                Reason
                            </label>
                            <x-inputs.text-area
                                id="{{ $ban->id }}_reason"
                                name="reason"
                                required
                            >
                                {{ $ban->ban_reason }}
                            </x-inputs.text-area>
                        </div>
                        <div class="mt-6 flex items-center gap-4 flex-col sm:flex-row">
                            <label for="{{ $ban->id }}_startDate">Start Date*</label>
                            <x-inputs.date
                                    type="datetime-local"
                                    id="{{ $ban->id }}_startDate"
                                    name="startDate"
                                    value="{{ $ban->start_date }}"
                                    required
                            />
                            <label class="sm:ml-12" for="{{ $ban->id }}_endDate">End Date*</label>
                            <x-inputs.date
                                    type="datetime-local"
                                    id="{{ $ban->id }}_endDate"
                                    name="endDate"
                                    value="{{ $ban->end_date }}"
                                    required
                            />
                        </div>
                        <span class="text-sm text-slate-500">
                            *Timezone is UTC+2 (Europe/Berlin)
                        </span>
                        <div class="flex gap-4 mt-4">
                            <x-inputs.button class="save"
                                            name="method"
                                             value="{{ \App\APIClients\HttpMethod::PUT }}"
                            >
                                Save
                            </x-inputs.button>
                            <x-inputs.button class="delete"
                                             name="method"
                                             value="{{ \App\APIClients\HttpMethod::DELETE }}"
                            >
                                Delete
                            </x-inputs.button>
                        </div>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
        <a href="{{ route('user.ban.create', ['user' => $user->id]) }}" class="flex justify-center">
            <x-inputs.button type="button" class="delete">
                <div class="flex justify-center items-center gap-2">
                    <span>
                        Add {{ count($bans) <= 0 ? '' : 'another' }} Ban
                    </span>
                    <x-icons.hammer class="size-6"/>
                </div>
            </x-inputs.button>
        </a>
</x-layouts.admin>