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

    $allowEdit = $user->can(\App\Enums\Auth\Permissions::EDIT_USERS->value);
    $allowEdit = false;
@endphp

<x-layouts.admin>
    <div class="w-full h-full user-details">
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
                </div>
            </div>
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
                    <label>Last Faction</label>
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
                                id="lastHunter"
                                name="lastHunter"
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
                    <span class="text-yellow-500">{{ $user->playerData()->runner_faction_level }}</span>
                </div>
                <x-misc.progression.faction-progression
                        class="p-2"
                        :current="$user->playerData()->runner_faction_experience"
                        :needed="PlayerData::getRemainingFactionExperience($user->playerData()->runner_faction_level)"
                />
            </div>
            <div class="bg-red-400 dark:bg-red-800 p-2 rounded-md col-start-2 shadow-glow shadow-red-400/15 outline outline-red-900">
                <div class="flex justify-between p-2 text-2xl font-bold ">
                    <label>Hunter</label>
                    <span class="text-yellow-500">{{ $user->playerData()->hunter_faction_level }}</span>
                </div>
                <x-misc.progression.faction-progression
                        class="p-2"
                        :current="$user->playerData()->hunter_faction_experience"
                        :needed="PlayerData::getRemainingFactionExperience($user->playerData()->hunter_faction_level)"
                />
            </div>
        </div>
    </div>
</x-layouts.admin>