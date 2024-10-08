<x-inputs.buttons.login-button class="fixed top-0 right-0 m-2" href="{{ route(\App\Http\Controllers\Web\LoginController::ROUTE_LOGIN) }}" />

<div class="flex flex-col items-center text-center absolute top-[50%] left-[50%] -translate-x-1/2 -translate-y-1/2">
    <img src="{{ asset('img/logos/DG_Rebirth_Logo.png') }}" alt="Deathgarden Bloodharves Logo" class="mb-2">

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