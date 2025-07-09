@php
    use App\Enums\Game\ExperienceEventType;

    $experienceMultipliers = \App\Models\Admin\ExperienceMultipliers::get();
    $currencyMultipliers = \App\Models\Admin\CurrencyMultipliers::get();
    $matchmakingSettings = \App\Models\Admin\MatchmakingSettings::get();
@endphp

<x-layouts.admin>
    <div class="container mx-auto border bg-slate-800 border-slate-500 rounded-xl  p-4 my-6">
        <div class="flex flex-col gap-4 px-4 lg:[&>form>div]:px-52 [&>form>div]:flex [&>form>div]:justify-between [&>form>div]:items-center [&>form_label]:text-xl">
            <form action="{{ route('match-configuration.save.experience') }}" method="post" class="[&>div]:m-2">
                @csrf
                <span class="headline mb-2">
                    Experience Multipliers
                </span>

                <div>
                    <label for="construct-defeats">
                        Construct Defeat
                    </label>

                    <x-inputs.number
                            id="construct-defeats"
                            name="construct-defeats"
                            value="{{ $experienceMultipliers->getEventTypeMultiplier(ExperienceEventType::ConstructDefeats) }}"
                            step="0.01"
                            required
                    />
                </div>


                <div>
                    <label for="downing">
                        Downing
                    </label>

                    <x-inputs.number
                            id="downing"
                            name="downing"
                            value="{{ $experienceMultipliers->getEventTypeMultiplier(ExperienceEventType::Downing) }}"
                            step="0.01"
                            required
                    />
                </div>
                <div>
                    <label for="drones">
                        Drones
                    </label>

                    <x-inputs.number
                            id="drones"
                            name="drones"
                            value="{{ $experienceMultipliers->getEventTypeMultiplier(ExperienceEventType::Drones) }}"
                            step="0.01"
                            required
                    />
                </div>
                <div>
                    <label for="execution">
                        Execution
                    </label>

                    <x-inputs.number
                            id="execution"
                            name="execution"
                            value="{{ $experienceMultipliers->getEventTypeMultiplier(ExperienceEventType::Execution) }}"
                            step="0.01"
                            required
                    />
                </div>
                <div>
                    <label for="garden-finale">
                        Garden Finale
                    </label>

                    <x-inputs.number
                            id="garden-finale"
                            name="garden-finale"
                            value="{{ $experienceMultipliers->getEventTypeMultiplier(ExperienceEventType::GardenFinale) }}"
                            step="0.01"
                            required
                    />
                </div>
                <div>
                    <label for="hacking">
                        Hacking
                    </label>

                    <x-inputs.number
                            id="hacking"
                            name="hacking"
                            value="{{ $experienceMultipliers->getEventTypeMultiplier(ExperienceEventType::Hacking) }}"
                            step="0.01"
                            required
                    />
                </div>
                <div>
                    <label for="hunter-close">
                        Hunter Close
                    </label>

                    <x-inputs.number
                            id="hunter-close"
                            name="hunter-close"
                            value="{{ $experienceMultipliers->getEventTypeMultiplier(ExperienceEventType::HunterClose) }}"
                            step="0.01"
                            required
                    />
                </div>
                <div>
                    <label for="resources">
                        Resources
                    </label>

                    <x-inputs.number
                            id="resources"
                            name="resources"
                            value="{{ $experienceMultipliers->getEventTypeMultiplier(ExperienceEventType::Resources) }}"
                            step="0.01"
                            required
                    />
                </div>
                <div>
                    <label for="team-actions">
                        Team Actions
                    </label>

                    <x-inputs.number
                            id="team-actions"
                            name="team-actions"
                            value="{{ $experienceMultipliers->getEventTypeMultiplier(ExperienceEventType::TeamActions) }}"
                            step="0.01"
                            required
                    />
                </div>


                <x-inputs.button class="save mt-2">
                    Save
                </x-inputs.button>
            </form>
            <form action="{{ route('match-configuration.save.currency') }}" method="post" class="mt-8 [&>div]:m-2">
                @csrf
                <span class="headline">
                    Currency Multipliers
                </span>

                <div>
                    <label for="currencyA">
                        Iron
                    </label>

                    <x-inputs.number
                            id="currencyA"
                            name="currencyA"
                            value="{{ $currencyMultipliers->currencyA }}"
                            step="0.01"
                            required
                    />
                </div>

                <div>
                    <label for="currencyB">
                        Blood Cells
                    </label>

                    <x-inputs.number
                            id="currencyB"
                            name="currencyB"
                            value="{{ $currencyMultipliers->currencyB }}"
                            step="0.01"
                            required
                    />
                </div>

                <div>
                    <label for="currencyC">
                        Ink Cells
                    </label>

                    <x-inputs.number
                            id="currencyC"
                            name="currencyC"
                            value="{{ $currencyMultipliers->currencyC }}"
                            step="0.01"
                            required
                    />
                </div>
                <x-inputs.button class="save mt-2">
                    Save
                </x-inputs.button>
            </form>
            <form action="{{ route('match-configuration.save.matchmaking') }}" method="post" class="mt-8 [&>div]:m-2">
                @csrf
                <span class="headline mb-2">
                    Matchmaking Settings
                </span>

                <div>
                    <label for="matchmakingWaitingTime">
                        1v4 and 1v5 matchmaking wait duration
                    </label>

                    <x-inputs.number
                            id="matchmakingWaitingTime"
                            name="matchmakingWaitingTime"
                            value="{{ $matchmakingSettings->matchWaitingTime }}"
                            step="1"
                            required
                    />
                </div>
                <x-inputs.button class="save mt-2">
                    Save
                </x-inputs.button>
            </form>
        </div>
    </div>
</x-layouts.admin>