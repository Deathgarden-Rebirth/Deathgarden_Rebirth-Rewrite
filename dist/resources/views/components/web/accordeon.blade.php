@props([
    'headline'
])

<div x-data="{open: false}" {{ $attributes->merge(['class' => 'bg-gray-900 px-4 py-4 rounded border border-gray-800']) }}>
    <h2 class="text-2xl font-semibold hover:after:bg-web-main after:transition-colors after:duration-200 cursor-pointer" x-on:click="open = !open" >
        <div class="flex justify-between">
            <span>{!! $headline !!}</span>
            <x-icons.chevron class="size-8 min-w-8 min-h-8 transition-transform" x-bind:class="{'rotate-90': !open}"/>
        </div>
    </h2>
    <div x-show="open" x-transition class="pl-6 mt-4" style="display: none;">
        {{ $slot }}
    </div>
</div>