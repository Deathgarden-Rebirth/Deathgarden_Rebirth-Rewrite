<?php
    /** @var StringBackedEnum[] $cases */
    /** @var StringBackedEnum $selected */
?>

<select {{ $attributes->merge(['class' => 'input-global-dropdown']) }}>
    @foreach($cases as $case)
        <option value="{{ $case->value }}"
                @selected($case === $selected)
        >
            {{ $case->value }}
        </option>
    @endforeach
</select>