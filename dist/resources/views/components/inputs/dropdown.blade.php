<?php
    /** @var StringBackedEnum[] $cases */
    /** @var StringBackedEnum $selected */
?>

@props([
    'disabled' => false,
])


<select {{ $attributes->merge(['class' => 'input-global-dropdown']) }} @disabled($disabled)>
    @foreach($cases as $case)
        <option value="{{ $case->value }}"
                @selected($case === $selected)
        >
            {{ $case->value }}
        </option>
    @endforeach
</select>