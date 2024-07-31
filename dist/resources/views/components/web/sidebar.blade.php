<div class="bg-slate-700 h-full grid grid-cols-[auto_1fr] grid-rows-[auto_100%]">
    <div class="bg-slate-500 row-start-1 py-4 px-2 gap-4 flex justify-between items-center">
        @auth
            @php
                $user = Auth::user();
            @endphp

            <a href="{{ route('logout') }}" title="Logout" class="w-8 ml-2.5">
                <x-icons.logout class="-scale-x-100"/>
            </a>

            <div class="max-h-10 object-scale-down w-20 mr-2">
                <x-auth.user :$user/>
            </div>
        @endauth

        @guest
            <x-inputs.buttons.login-button/>
        @endguest
    </div>
    <div class="bg-slate-600 row-start-2 p-4 flex flex-col text-xl gap-4">
        <a href="#">
            <div class="flex mr-4 hover:translate-x-4 transition-transform">
                <span class="font-bold hover">Credits Long Text</span>
            </div>
        </a>
        <a href="#">
            <div class="flex mr-4 hover:translate-x-4 transition-transform">
                <span class="font-bold hover">Short</span>
            </div>
        </a>
        <div class="flex mr-4 hover:translate-x-4 transition-transform">
            <span class="font-bold hover">Really long Long Text</span>
        </div>


    </div>
</div>