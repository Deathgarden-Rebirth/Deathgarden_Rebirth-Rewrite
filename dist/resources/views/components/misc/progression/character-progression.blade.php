@props([
    'allowEdit' => false,
])

@php
    /** @var \App\Models\Game\CharacterData $character */
@endphp

<div {{ $attributes->merge(['class' => 'p-2']) }}>
    <div class="flex justify-between text-xl font-bold">
        <span>{{ $character->character->name }}</span>
        @if($allowEdit)
            <x-inputs.number
                    class="w-20"
                    id="{{ $character->character->value }}Level"
                    name="{{ $character->character->value }}Level"
                    value="{{ $character->level }}"
                    min="1"
            />
        @else
            <span class="text-yellow-500">{{ $character->level }}</span>
        @endif
    </div>
    <div class="w-full bg-gray-800 min-h-2 mt-1">
        <div class="bg-sky-400" style="width: {{ ($progress * 100).'%' }}">
            <span class="text-nowrap p-2">
                {{ number_format($character->experience, thousands_separator: '.').' | '.number_format($neededExperience, thousands_separator: '.') }}
            </span>
        </div>
    </div>
</div>