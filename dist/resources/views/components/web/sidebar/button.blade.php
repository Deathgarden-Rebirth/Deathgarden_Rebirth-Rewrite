@props([
    'routeName'
])

@php
    /** @var \Illuminate\View\ComponentAttributeBag $attributes */

@endphp

<a href="{{ route($routeName) }}">
    <div {{ $attributes->except('href')->merge(['class' => "px-2 py-1 outline-1 hover:outline hover:outline-slate-600 hover:bg-slate-700 rounded " . (Route::getCurrentRoute()->getName() === $routeName ? 'bg-web-main' : '')]) }} >
        {{ $slot }}
    </div>
</a>