@props([
    'user'
])

@pushonce('head')
    @vite(['resources/css/components/admin/tools/user-details.scss'])
@endpushonce

@php
    /** @var \App\Models\User\User $user */

    use App\Enums\Game\Faction;
    use App\Enums\Game\Runner;
    use App\Enums\Game\Hunter;
    use App\Models\User\PlayerData;

    $allowEdit = \Illuminate\Support\Facades\Auth::user()->can(\App\Enums\Auth\Permissions::EDIT_USERS->value);
@endphp

<x-layouts.admin>
    <div class="w-full user-details max-h-full bg-inherit overflow-y-auto">
        @if($allowEdit)
            <form action="{{ route('user.edit', ['user' => $user->id]) }}" method="post">
                @csrf
                @endif
                <div class="section">
                    <h1>General Info</h1>
                    <div class="flex gap-4 mt-4 items-center">
                        <img src="{{ $user->avatar_full }}" class="rounded-xl border-2 border-indigo-600 aspect-square"
                             alt="Avatar">
                        <div class="flex flex-col flex-wrap w-full justify-around bg-slate-800 px-6 rounded-xl border-2 border-indigo-600">
                            <div class="attribute">
                                <label>ID</label>
                                <span class="border-none">{{ $user->id }}</span>
                            </div>
                            <div class="attribute">
                                <label>Steam ID</label>
                                <span class="border-none">{{ $user->steam_id }}</span>
                            </div>
                            <div class="attribute">
                                <label>Last known Username</label>
                                <span class="border-none">{{ $user->last_known_username }}</span>
                            </div>
                            <div class="attribute">
                                <label>Created At</label>
                                <span class="border-none">{{ $user->created_at }}</span>
                            </div>
                            <div class="attribute">
                                <label>Source</label>
                                <span class="border-none">{{ $user->source }}</span>
                            </div>
                            <div class="attribute">
                                <label>Ban Status</label>
                                <x-misc.ban-status class="border-none" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section flex gap-2 justify-center !p-4">
                    <a href="">
                        <x-inputs.button type="button" title="Not yet Implemented">
                        <span class="flex items-center gap-2 text-xl align-middle">
                            <x-icons.mail class="size-5"/>
                            Inbox
                        </span>
                        </x-inputs.button>
                    </a>

                    <a href="{{ route('user.bans', ['user' => $user->id]) }}">
                        <x-inputs.button class="delete" type="button">
                        <span class="flex items-center gap-2 text-xl align-middle">
                            <x-icons.hammer class="size-5"/>
                            Bans
                        </span>
                        </x-inputs.button>
                    </a>
                </div>

                <div class="section">
                    <h1>Player Data</h1>
                    <div class="md:columns-3 mt-2">
                        <div class="attribute">
                            <label>Iron</label>
                            @if($allowEdit)
                                <x-inputs.number
                                        id="currencyA"
                                        name="currencyA"
                                        value="{{ $user->playerData()->currency_a }}"
                                />
                            @else
                                <span>{{ $user->playerData()->currency_a }}</span>
                            @endif
                        </div>
                        <div class="attribute">
                            <label>Blood Cells</label>
                            @if($allowEdit)
                                <x-inputs.number
                                        id="currencyB"
                                        name="currencyB"
                                        value="{{ $user->playerData()->currency_b }}"
                                />
                            @else
                                <span>{{ $user->playerData()->currency_b }}</span>
                            @endif
                        </div>
                        <div class="attribute">
                            <label>Ink Cells</label>
                            @if($allowEdit)
                                <x-inputs.number
                                        id="currencyC"
                                        name="currencyC"
                                        value="{{ $user->playerData()->currency_c }}"
                                />
                            @else
                                <span>{{ $user->playerData()->currency_c }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="md:columns-3 mt-4">
                        <div class="attribute">
                            <label>Last Faction</label>
                            @if($allowEdit)
                                <x-inputs.dropdown
                                        id="lastFaction"
                                        name="lastFaction"
                                        :cases="Faction::cases()"
                                        :selected="$user->playerData()->last_faction"
                                />
                            @else
                                <span>{{ $user->playerData()->last_faction }}</span>
                            @endif
                        </div>
                        <div class="attribute">
                            <label>Last Runner</label>
                            @if($allowEdit)
                                <x-inputs.dropdown
                                        id="lastRunner"
                                        name="lastRunner"
                                        :cases="Runner::cases()"
                                        :selected="$user->playerData()->last_runner"
                                />
                            @else
                                <span>{{ $user->playerData()->last_runner }}</span>
                            @endif
                        </div>
                        <div class="attribute">
                            <label>Last Hunter</label>
                            @if($allowEdit)
                                <x-inputs.dropdown
                                        id="lastHunter"
                                        name="lastHunter"
                                        :cases="Hunter::cases()"
                                        :selected="$user->playerData()->last_hunter"
                                />
                            @else
                                <span>{{ $user->playerData()->last_hunter }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="columns-1 mt-4">
                        <div class="attribute">
                            <label>Has Played DG 1</label>
                            @if($allowEdit)
                                <x-inputs.checkbox
                                        class="size-6"
                                        id="hasPlayedDG1"
                                        name="hasPlayedDG1"
                                        :checked="$user->playerData()->has_played_dg_1"
                                />
                            @else
                                <span>{{ $user->playerData()->has_played_dg_1 ? 'Yes' : 'No' }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="section md:grid md:grid-cols-2 md:gap-4">
                    <h1 class="col-span-full">Progression</h1>
                    <div class="bg-green-400 dark:bg-green-800 p-2 rounded-md shadow-glow shadow-green-400/15 outline outline-green-700">
                        <div class="flex justify-between p-2 text-2xl font-bold ">
                            <label>Runner</label>
                            @if($allowEdit)
                                <x-inputs.number
                                        class="w-32"
                                        id="RunnerFactionLevel"
                                        name="runnerFactionLevel"
                                        value="{{ $user->playerData()->runner_faction_level }}"
                                        min="1"
                                />
                            @else
                                <span class="text-yellow-500">{{ number_format($user->playerData()->runner_faction_level, thousands_separator: '.') }}</span>
                            @endif
                        </div>
                        <x-misc.progression.faction-progression
                                class="p-2"
                                :current="$user->playerData()->runner_faction_experience"
                                :needed="PlayerData::getRemainingFactionExperience($user->playerData()->runner_faction_level)"
                        />
                        @foreach(Runner::cases() as $character)
                            <x-misc.progression.character-progression
                                    class="mt-1 bg-gray-500/50 rounded-md"
                                    :allowEdit="$allowEdit"
                                    :character="$user->playerData()->characterDataForCharacter(\App\Enums\Game\Characters::from($character->value))"/>
                        @endforeach
                    </div>
                    <div class="bg-red-400 dark:bg-rose-800 p-2 rounded-md col-start-2 shadow-glow shadow-red-400/15 outline outline-red-900 mt-4 md:mt-0">
                        <div class="flex justify-between p-2 text-2xl font-bold ">
                            <label>Hunter</label>
                            @if($allowEdit)
                                <x-inputs.number
                                        class="w-32"
                                        id="HunterFactionLevel"
                                        name="hunterFactionLevel"
                                        value="{{ $user->playerData()->hunter_faction_level }}"
                                        min="1"
                                />
                            @else
                                <span class="text-yellow-500">{{ number_format($user->playerData()->hunter_faction_level, thousands_separator: '.') }}</span>
                            @endif
                        </div>
                        <x-misc.progression.faction-progression
                                class="p-2"
                                :current="$user->playerData()->hunter_faction_experience"
                                :needed="PlayerData::getRemainingFactionExperience($user->playerData()->hunter_faction_level)"
                        />
                        @foreach(Hunter::cases() as $character)
                            <x-misc.progression.character-progression
                                    class="mt-1 bg-gray-500/50 rounded-md"
                                    :allowEdit="$allowEdit"
                                    :character="$user->playerData()->characterDataForCharacter(\App\Enums\Game\Characters::from($character->value))"/>
                        @endforeach
                    </div>

                    @if($allowEdit)
                        <div class="flex gap-2">
                            <x-inputs.button class="save">
                                Save Changes
                            </x-inputs.button>
                            <x-inputs.button
                                    class="delete"
                                    rel="modal:open"
                                    type="button"
                                    href="#reset-player-modal"
                            >
                                Reset Player
                            </x-inputs.button>
                            <div class="modal" id="reset-player-modal">
                                <div class="flex flex-col items-center gap-5 text-center">
                                    <span>
                                        Are you sure you want to reset the player Data?<br>
                                        Doing so will Erase all the player and character progress of this user.<br>
                                        <strong>(This is <span class="text-red-600">NOT</span> reversible)</strong>
                                    </span>
                                    <div class="flex gap-5">
                                        <x-inputs.button type="button" href="#close" rel="modal:close">
                                            Cancel
                                        </x-inputs.button>
                                        <x-inputs.button
                                                form="reset-form"
                                                class="delete"
                                                formnovalidate
                                        >
                                            Delete
                                        </x-inputs.button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                @if($allowEdit)
            </form>
            <form id="reset-form"
                  action="{{ route('user.reset', ['user' => $user->id]) }}"
                  method="post">@csrf</form>
        @endif
    </div>
</x-layouts.admin>