<x-layouts.app>
    <div class="h-screen flex bg-inherit">
        <x-web.sidebar/>
        <div class="row-span-2 h-full bg-inherit">
            {{ $slot }}
        </div>
    </div>
</x-layouts.app>