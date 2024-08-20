@php
    /** @var \App\Models\GameFile[] $files */
@endphp

@props([
    'files' => [],
])

<x-layouts.web>
    <div class="container flex flex-col justify-center items-center mx-auto py-8 2xl:px-48">
        <x-web.headline class="w-full">
            Deathgarden Rebirth Launcher
        </x-web.headline>

        <x-web.text class="">
            The game launcher, available only for Windows, is essential for updating and playing
            Deathgarden: Rebirth.
        </x-web.text>
        <x-web.text>
            By downloading the launcher, you'll also be able to see how many players
            are online and in the queue, keeping you informed and ready to jump into the action.
            Please note that you'll still need to have Steam open to log in.
        </x-web.text>

        <a href="{{ route('download') }}">
            <x-inputs.button type="button"
                             class="my-12 !px-8 !py-6 !bg-web-main hover:scale-110 !transition-all !border-web-main">
            <span class="text-3xl font-semibold">
                Download for Windows
            </span>
            </x-inputs.button>
        </a>

        <x-web.accordeon headline="If you're a Linux player, you can download the latest update files directly from here."
                         class="w-full"
        >
            @if(count($files) > 0)
                @foreach($files as $file)
                    @dump($file)
                @endforeach
            @else
                There are no Files uploaded yet.
            @endif
        </x-web.accordeon>


    </div>
</x-layouts.web>