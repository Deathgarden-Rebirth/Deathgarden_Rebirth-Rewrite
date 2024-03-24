<?php

    use App\Http\Controllers\Web\Admin\Tools\AdminToolController
?>

<x-layouts.admin>
    <div class="flex flex-wrap justify-center align-middle content-center h-full p-10 items-center gap-6">
        @foreach(AdminToolController::getAllTools() as $toolClass)
            @php
                // Don't display tool if user doesn't have Permission.
                $user = \Illuminate\Support\Facades\Auth::user();
                if($user->cant($toolClass::getNeededPermission()))
                    continue;
            @endphp

            <a href="{{ route($toolClass) }}" class="justify-center text-center">
            <div class="bg-slate-300 dark:bg-gray-800 w-48 p-4 rounded-xl flex flex-col items-center h-56 max-h-96">
                <span>
                    {{ $toolClass::getName() }}
                </span>
                <hr class="w-full m-2">
                <x-dynamic-component :component="$toolClass::getIconComponent()" class="w-12 mb-2" />
                <span>
                    {{ $toolClass::getDescription() }}
                </span>
            </div>
            </a>
        @endforeach
    </div>
</x-layouts.admin>