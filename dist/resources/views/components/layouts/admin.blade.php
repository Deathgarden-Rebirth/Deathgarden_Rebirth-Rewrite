<x-layouts.app>
    <div class="h-screen flex flex-col">
        <x-admin.navbar class="row-span-1"/>
        <div class="row-span-2 h-full">
            {{ $slot }}
        </div>
    </div>
</x-layouts.app>