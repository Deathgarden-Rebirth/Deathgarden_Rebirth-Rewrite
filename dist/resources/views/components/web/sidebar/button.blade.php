@php
    /** @var \Illuminate\View\ComponentAttributeBag $attributes */
@endphp

<a href="{{ $attributes->get('href', '#') }}">
    <div {{ $attributes->except('href')->merge(['class' => "px-2 py-1 outline-1 hover:outline hover:outline-slate-600 hover:bg-slate-700 rounded"]) }} >
        {{ $slot }}
    </div>
</a>