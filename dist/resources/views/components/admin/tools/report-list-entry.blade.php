@props([
    'report'
])

@php
    /** @var \App\Models\Game\Moderation\PlayerReport $report */
@endphp

<form action="{{ route('reports.handle', ['report' => $report->id]) }}" id="{{ $report->id }}form" method="post">
    @csrf
    <tr>
        <td class="px-8">
            {{ $report->created_at }}
        </td>
        <td class="px-8">
            <a href="{{ route('user.details', ['user' => $report->reportingUser->id]) }}" target="_blank">
                <div class="col-start-1 flex gap-2 justify-center items-center">
                    <img src="{{ $report->reportingUser->avatar_small }}"
                         alt="Profile Picture of {{ $report->reportingUser->last_known_username }}">
                    <span>
                        {{ $report->reportingUser->last_known_username ?? $report->reportingUser->id }}
                    </span>
                </div>
            </a>
        </td>
        <td class="px-8">
            <a href="{{ route('user.details', ['user' => $report->reportedUser->id]) }}" target="_blank">
                <div class="col-start-1 flex gap-2 justify-center items-center">
                    <img src="{{ $report->reportedUser->avatar_small }}"
                         alt="Profile Picture of {{ $report->reportedUser->last_known_username }}">
                    <span>
                        {{ $report->reportedUser->last_known_username ?? $report->reportedUser->id }}
                    </span>
                </div>
            </a>
        </td>
        <td>
            {{ $report->reason }}
        </td>
        <td>
            <div class="bg-slate-700 border border-slate-500 p-2 rounded-md">
                {{ $report->details ?? '---' }}
            </div>
        </td>

        <td>
            <x-inputs.button
                    type="button"
                    href="#{{ $report->id }}extra-info"
                    rel="modal:open"
            >
                <span class="size-6">
                    Show
                </span>
            </x-inputs.button>
            <div id="{{ $report->id }}extra-info" class="modal">
                <div class="flex flex-col gap-4">
                    <div class="flex gap-4">
                        <span class="font-bold w-24">Match ID</span>
                        <span>{{ $report->match_id }}</span>
                    </div>
                    <div class="flex flex-col gap-4">
                        <span class="font-bold w-24">Player-infos</span>
                        @foreach($report->playerInfos() as $player)
                            <div @class([
                                'flex flex-col gap-2 rounded-md p-6',
                                'bg-slate-600' => !$player->isReportingPlayer && !$player->isReportedPlayer,
                                'bg-blue-600' => $player->isReportingPlayer,
                                'bg-rose-800' => $player->isReportedPlayer,
                            ])>
                                <div>
                                    <span class="font-bold mr-4">Player</span>
                                    <a href="{{ route('user.details', ['user' => $player->playerId]) }}"
                                       target="_blank">
                                        <span class="underline">{{ $player->getPlayerName() ?? $player->playerId }}</span>
                                    </a>
                                    @if($player->isReportedPlayer)
                                        <span class="italic">
                                            (Reported)
                                        </span>
                                    @endif
                                    @if($player->isReportingPlayer)
                                        <span class="italic">
                                            (Reporting)
                                        </span>
                                    @endif
                                </div>
                                <div>
                                    <span class="font-bold mr-4">State</span>
                                    <span>{{ $player->characterState->value }}</span>
                                </div>
                                <div>
                                    <span class="font-bold mr-4">Faction</span>
                                    <span>{{ $player->faction->value }}</span>
                                </div>
                                <div>
                                    <span class="font-bold mr-4">Total XP Earned</span>
                                    <span>{{ $player->totalXpEarned }}</span>
                                </div>
                                <div>
                                    <span class="font-bold mr-4">Playtime in seconds</span>
                                    <span>{{ $player->playtimeInSeconds }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </td>

        @if(!$report->handled)
            <td>
                <div class="flex items-center justify-center gap-2">
                    <x-inputs.button
                            type="button"
                            class="save"
                            href="#{{ $report->id }}handle-modal"
                            rel="modal:open"
                    >
                        <span class="size-6">
                            Handle
                        </span>
                    </x-inputs.button>
                    <a href="{{ route('user.bans', ['user' => $report->reportedUser->id]) }}" target="_blank">
                        <x-inputs.button type="button" class="px-2 py-1 !text-sm delete" title="Bans">
                            <x-icons.hammer class="size-6"/>
                        </x-inputs.button>
                    </a>
                    <a href="{{ route('user.inbox', ['user' => $report->reportedUser->id]) }}" target="_blank">
                        <x-inputs.button type="button" class="px-2 py-1 !text-sm" title="Inbox">
                            <x-icons.mail class="size-6"/>
                        </x-inputs.button>
                    </a>
                </div>

                <div id="{{ $report->id }}handle-modal" class="modal">
                    <div class="flex flex-col items-center gap-5">
                        <label for="{{ $report->id }}consequences">
                            Consequences
                        </label>
                        <x-inputs.text-area
                                form="{{ $report->id }}form"
                                id="{{ $report->id }}consequences"
                                class="!w-full"
                                name="consequences"
                        />

                        <div class="flex gap-5">
                            <x-inputs.button type="button" href="#close" rel="modal:close">
                                Cancel
                            </x-inputs.button>
                            <x-inputs.button
                                    form="{{ $report->id }}form"
                                    class="create"
                                    formnovalidate
                            >
                                Mark as Handled
                            </x-inputs.button>
                        </div>
                    </div>
                </div>
            </td>
        @else
            <td>
                {{ $report->consequences }}
            </td>
            <td>
                @if($report->handledBy === null)
                    Handler Unknown
                @else
                    <div class="col-start-1 flex gap-2 justify-center items-center">
                        <img src="{{ $report->handledBy?->avatar_small }}"
                             alt="Profile Picture of {{ $report->handledBy?->last_known_username }}">
                        <span>
                            {{ $report->handledBy?->last_known_username ?? $report->handledBy?->id }}
                        </span>
                    </div>
                @endif
            </td>
        @endif
    </tr>
</form>