<?php
    /** @var StringBackedEnum[] $cases */
    /** @var StringBackedEnum $selected */
?>

@props([
    'disabled' => false,
])


<select {{ $attributes->merge(['class' => 'input-global-dropdown']) }} @disabled($disabled)>
    @foreach($cases as $case)
        <option value="{{ $case instanceof BackedEnum ? $case->value : $case }}"
                @selected($case === $selected)
        >
            {{ is_string($case) ? $case : ($case instanceof StringBackedEnum ? $case->value : $case->name) }}
        </option>
    @endforeach
</select>