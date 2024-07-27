@php

    use App\Http\Controllers\Web\Admin\Tools\AdminToolController;
@endphp

<x-layouts.admin>
    <div class="flex flex-wrap justify-center align-middle content-center h-full p-10 items-center gap-6">
        @foreach(AdminToolController::getAllTools() as $toolClass)
            @php
                // Don't display tool if user doesn't have Permission.
                $user = \Illuminate\Support\Facades\Auth::user();
                if($user->cant($toolClass::getNeededPermission()))
                    continue;

                $notificationText = $toolClass::getNotificationText()
            @endphp

            <a href="{{ route($toolClass) }}" class="justify-center text-center">
            <div class="relative bg-slate-300 dark:bg-gray-800 w-48 p-4 rounded-xl flex flex-col items-center h-56 max-h-96">
                @if($notificationText !== null)
                        <div class="absolute -top-[3%] left-[93%] bg-yellow-300 w-6 h-6 rounded-[50%] shadow-glow shadow-yellow-300 flex justify-center items-center"
                             title="{{ $notificationText }}">
                            <x-icons.bell class="stroke-black size-5"/>
                        </div>
                @endif


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