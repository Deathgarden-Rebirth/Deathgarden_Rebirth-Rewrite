@props([
    'headline'
])

<div x-data="{open: false}" {{ $attributes }}>
    <x-web.headline class="!text-2xl hover:after:bg-web-main after:transition-colors after:duration-200 cursor-pointer" x-on:click="open = !open" >
        <div class="flex justify-between">
            <span>{{ $headline }}</span>
            <x-icons.chevron class="size-8 transition-transform" x-bind:class="{'rotate-90': !open}"/>
        </div>
    </x-web.headline>
    <div x-show="open" x-transition class="pl-6 -mt-2">
        {{ $slot }}
    </div>
</div>