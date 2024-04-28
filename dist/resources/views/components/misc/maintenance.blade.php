<a href="{{ route('login') }}" class="fixed top-0 right-0 m-2">
    <x-inputs.button type="button" class="hover:bg-indigo-700">
        <div class="flex gap-2 align-middle items-center">
            <span class="h-min">Admin Login</span>
            <x-icons.steam class="w-6"/>
        </div>
    </x-inputs.button>
</a>

<div class="flex flex-col items-center text-center absolute top-[50%] left-[50%] -translate-x-1/2 -translate-y-1/2">
    <img src="{{ asset('img/logos/DG_BH_Logo.png') }}" alt="Deathgarden Bloodharves Logo">

    <h1 class="text-7xl mb-6 mt-[-1em]">Rebirth</h1>
    <p class="text-4xl">
        This page is currently under construction. <br>
        Keep up to date on our Discord in the meantime!
    </p>

    <a href="https://tinyurl.com/chronosdiscord" target="_blank">
        <div>
            <x-icons.discord class="w-24 mt-6 hover:fill-indigo-600"/>
        </div>
    </a>
</div>