@php
    /** @var \Illuminate\View\ComponentAttributeBag $attributes */

    $addAnchorTagForModal = $attributes->has('rel') && str_contains($attributes->get('rel'), 'modal');
@endphp

@props([
    'type' => 'submit'
])

@if($addAnchorTagForModal)
    <a href="{{ $attributes->get('href') }}" rel="{{ $attributes->get('rel') }}">
@endif

<button type="{{ $type }}" {{ $attributes->merge(['class' => 'input-global-submit']) }}>
    <span class="text-inherit">{{ $slot }}
    </span>
</button>

@if($attributes)
    </a>
@endif