@php
    /** @var \Illuminate\View\ComponentAttributeBag $attributes */

    if(!$attributes->has('href')) {
        $attributes->setAttributes([
            'href' => route('login'),
            ...$attributes->getAttributes()
        ]);
    }
@endphp

<a {{ $attributes }}>
    <x-inputs.button type="button" class="hover:bg-indigo-700">
        <div class="flex gap-2 align-middle items-center">
            <span class="h-min">Login</span>
            <x-icons.steam class="w-6"/>
        </div>
    </x-inputs.button>
</a>
