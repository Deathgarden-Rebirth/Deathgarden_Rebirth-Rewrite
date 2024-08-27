@php
    /**
     * @var ?\App\Models\User\User $user
     * @var int $userSteamid
     * @var string $headline
     * @var ?string $usernameOverride
     * @var ?string $avatarOverride
     */

@endphp

<div {{ $attributes->merge(['class' => 'bg-gray-900 px-4 py-4 rounded border border-gray-800 flex gap-4 items-start']) }}>
    <div class="">
        <img src="{{ $avatarOverride ?? $user?->avatar_medium }}" alt="Avatar" class="rounded-md mx-1 size-14">
    </div>
    <div>
        <x-web.headline class="!mt-0">
            {{ $usernameOverride ?? $user?->last_known_username }} - {{ $headline }}
        </x-web.headline>
        {{ $slot }}
    </div>
</div>