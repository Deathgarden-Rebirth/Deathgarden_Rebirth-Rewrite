<?php
/** @var \App\Models\User\User $user */
?>

<nav class="bg-slate-300 dark:bg-gray-800 flex flex-row p-3 w-full items-center align-middle">
    <a href="/" title="return to website" class="w-6 mr-2.5">
        <x-icons.arrow-return-left />
    </a>

    <a href="{{ route('admin.dashboard') }}">
    <span class="font-bold text-lg mr-4">
        Administration
    </span>
    </a>

    @isset($title)
        <span class="italic">
            {{ $title }}
        </span>
    @endisset

    <div class="ml-auto max-h-10 object-scale-down">
        <x-auth.user :$user/>
    </div>

    <a href="{{ route('logout') }}" title="Logout" class="w-8 ml-2.5">
        <x-icons.logout />
    </a>
</nav>