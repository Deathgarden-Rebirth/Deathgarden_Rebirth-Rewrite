@php
    use App\Enums\Auth\Permissions;

    $agent = new \Phattarachai\LaravelMobileDetect\Agent();

    $initalOpen = !$agent->isMobile();

@endphp

<div class="bg-slate-900 h-full grid grid-cols-[auto_1fr] grid-rows-[auto_100%]"
     x-data="{
        open: false,
        toggle() {
            this.open = !this.open;
            localStorage.setItem('sidebarStatus', this.open)
        },
        init() {
            let localString = localStorage.getItem('sidebarStatus') ?? {{ $initalOpen ? 'true' : 'false' }};
            this.open = localString === 'true';
            console.log(this.open);
        }
     }"

>
    <div class="row-start-1 my-4 flex flex-col gap-4">
        <div class="flex mx-4 justify-between items-center" >
            <x-inputs.button type="button" class="size-10 flex justify-center items-center"
                             x-on:click="toggle()">
                <x-icons.bars class="size-6" x-show="!open" />
                <x-icons.plus class="size-6 rotate-45" x-show="open" />
            </x-inputs.button>

            <a href="{{ route('homepage') }}">
                <img src="{{ asset('img/logos/DG_BH_Logo.png') }}"
                     alt="Deathgarden Bloodharvest Logo"
                     class="h-14 -my-2 ml-4"
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
        <x-web.sidebar.button>
            <span class="font-bold">Download</span>
        </x-web.sidebar.button>

        <x-web.sidebar.button>
            <span class="font-bold">Credits</span>
        </x-web.sidebar.button>

        @can(Permissions::ADMIN_AREA->value)
            <x-web.sidebar.button href="{{ route('admin.dashboard') }}"
                                  class="flex justify-between items-center object-scale-down w-full">
                <span class="font-bold">Admin Dashboard</span>
                <x-icons.gear class="max-w-8"/>
            </x-web.sidebar.button>
        @endcan

        @auth
            <x-web.sidebar.button
                    href="{{ route('logout') }}"
                    class="flex justify-between items-center object-scale-down w-full text-rose-500 hover:text-inherit hover:!outline-rose-500 hover:!bg-rose-700">
                <span class="font-bold">Logout</span>
                <x-icons.logout class="max-w-8"/>
            </x-web.sidebar.button>
        @endauth

    </div>
</div>