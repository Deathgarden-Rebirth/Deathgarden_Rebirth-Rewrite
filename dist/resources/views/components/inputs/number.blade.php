@props([
    'disabled' => false,
])

<input type="number" @disabled($disabled) {{ $attributes->merge(['class' => 'input-global-number']) }}>