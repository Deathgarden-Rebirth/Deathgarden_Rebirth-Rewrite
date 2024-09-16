@pushonce('head')
    <x-misc.meta-tags />
@endpushonce

<x-layouts.app>
    <div class="h-screen flex bg-inherit">
        <x-web.sidebar/>
        <div class="row-span-2 h-full w-full bg-inherit overflow-x-auto flex flex-col justify-between ">
            <div class="px-4 md:px-16 lg:px-32">
                {{ $slot }}
            </div>

            <div class="w-full [background:linear-gradient(to_right,transparent,theme(colors.gray.900)_40%,theme(colors.gray.900)_60%,transparent)] border-t-2 border-web-main">
                <x-web.footer />
            </div>
        </div>
    </div>
</x-layouts.app>