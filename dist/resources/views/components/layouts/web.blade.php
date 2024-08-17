<x-layouts.app>
    <div class="h-screen flex bg-inherit">
        <x-web.sidebar/>
        <div class="row-span-2 h-full w-full px-4 md:px-16 lg:px-32 bg-inherit overflow-x-auto">
            {{ $slot }}
        </div>
    </div>
</x-layouts.app>