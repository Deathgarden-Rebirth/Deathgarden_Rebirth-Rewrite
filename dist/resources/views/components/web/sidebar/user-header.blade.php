@php
    /** @var \Illuminate\View\ComponentAttributeBag $attributes */
@endphp

<div {{ $attributes->except('x-transition') }} x-transition>
    @auth
        @php
            $user = Auth::user();
        @endphp

        <div class="max-h-10 object-scale-down w-full">
            <x-auth.user class="justify-between" :$user/>
        </div>
    @endauth

    @guest
        <x-inputs.buttons.login-button class="mx-4 justify-self-center"/>
    @endguest
</div>