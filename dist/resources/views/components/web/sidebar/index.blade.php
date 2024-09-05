@php
    use App\Enums\Auth\Permissions;

    $agent = new \Phattarachai\LaravelMobileDetect\Agent();

    $initalOpen = !$agent->isMobile();

@endphp

<div class="bg-slate-900 grid grid-cols-[auto_1fr] grid-rows-[auto_1fr] overflow-x-auto min-w-max transition-[width] ease-in-out duration-300"
     x-data="{
        open: false,
        toggle() {
            this.open = !this.open;
            localStorage.setItem('sidebarStatus', this.open)
        },
        init() {
            let localString = localStorage.getItem('sidebarStatus') ?? {{ $initalOpen ? 'true' : 'false' }};
            this.open = localString === 'true';
        }
     }"

     :class="{
        'w-0': !open,
        'w-56 max-w-max': open
     }"
>
    <x-inputs.button type="button" class="size-10 flex justify-center items-center hover:!bg-web-main focus:!border-web-main absolute top-4 left-4"
                     x-on:click="toggle()">
        <x-icons.bars class="size-6" x-show="!open" />
        <x-icons.plus class="size-6 rotate-45" x-show="open" />
    </x-inputs.button>
    <div class="row-start-1 my-4 flex flex-col gap-4" x-show="open" >
        <div class="flex mx-4 justify-between items-center" >
            <x-inputs.button type="button" class="size-10 flex justify-center items-center hover:!bg-web-main focus:!border-web-main opacity-0 pointer-events-none"
                             x-on:click="toggle()">
                <x-icons.plus class="size-6 rotate-45"/>
            </x-inputs.button>

            <a href="{{ route('homepage') }}" class="">
                <img src="{{ asset('img/logos/DG_Rebirth_Logo.png') }}"
                     alt="Deathgarden Bloodharvest Logo"
                     class="h-14 -my-2 ml-4 mr-2"
                     x-show="open"
                     x-transition
                >
            </a>
        </div>

        <x-web.sidebar.user-header
                class="bg-slate-700 mx-4 p-2 rounded gap-4 flex justify-center outline outline-1 outline-slate-600"
                x-show="open" x-transition/>
    </div>
    <div class="row-start-2 flex flex-col text-xl gap-4 px-4" x-show="open" x-transition >
        <x-web.sidebar.button routeName="download">
            <span class="font-bold">Download</span>
        </x-web.sidebar.button>

        <x-web.sidebar.button routeName="how-to-play">
                <span class="font-bold">How to Play</span>
        </x-web.sidebar.button>

        <x-web.sidebar.button href="{{ route('known-issues') }}" routeName="known-issues">
            <span class="font-bold">Known Issues</span>
        </x-web.sidebar.button>

        <x-web.sidebar.button routeName="eula">
            <span class="font-bold">EULA</span>
        </x-web.sidebar.button>

        <x-web.sidebar.button routeName="credits">
            <span class="font-bold">Credits</span>
        </x-web.sidebar.button>

        @can(Permissions::ADMIN_AREA->value)
            <x-web.sidebar.button routeName="admin.dashboard"
                                  class="flex justify-between items-center object-scale-down w-full">
                <span class="font-bold">Admin<br>Dashboard</span>
                <x-icons.gear class="size-8"/>
            </x-web.sidebar.button>
        @endcan

        @auth
            <x-web.sidebar.button
                    routeName="logout"
                    class="flex justify-between items-center object-scale-down w-full text-web-main hover:text-inherit hover:!outline-web-main hover:!bg-web-main">
                <span class="font-bold">Logout</span>
                <x-icons.logout class="max-w-8"/>
            </x-web.sidebar.button>
        @endauth

    </div>
</div>