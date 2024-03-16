<x-layouts.app>
    <div class="grid grid-cols-1 grid-rows-2">
        <x-admin.navbar class="row-span-1"/>
        <div class="row-span-2">
            {{ $slot }}
        </div>
    </div>
</x-layouts.app>