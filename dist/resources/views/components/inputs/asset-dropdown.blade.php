<?php
/** @var string[] $options */
/** @var string $selected */
?>

<select {{ $attributes->merge(['class' => 'input-global-dropdown']) }}>
    @foreach($options as $option)
        <option value="{{ $option }}"
                @selected($option === $selected)
        >
            {{ $option }}
        </option>
    @endforeach
</select>