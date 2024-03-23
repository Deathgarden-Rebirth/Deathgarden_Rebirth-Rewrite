@props([
    'checked' => false,
])

<input type="checkbox" {{ $attributes->merge(['class' => 'input-global-checkbox']) }} @checked($checked)>