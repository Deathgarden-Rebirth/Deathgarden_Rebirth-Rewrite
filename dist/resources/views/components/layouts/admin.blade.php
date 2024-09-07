<x-layouts.app>
    <div class="h-screen flex flex-col bg-inherit">
        <x-admin.navbar class="row-span-1"/>
        <div class="row-span-2 h-full bg-inherit">
            {{ $slot }}
        </div>
    </div>
</x-layouts.app>