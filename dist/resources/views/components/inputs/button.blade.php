@props([
    'type' => 'submit'
])

<button type="{{ $type }}" {{ $attributes->merge(['class' => 'input-global-submit']) }}><span class="text-inherit">{{ $slot }}</span></button>