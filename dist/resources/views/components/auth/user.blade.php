<?php
/** @var \App\Models\User\User $user */
/** @var string $profileUrl */
/** @var bool $showAvatar */
/** @var bool $showName */
?>

<div class="flex items-center" style="max-height: inherit">
    @if($showName)
        <a href="{{ $profileUrl }}" target="_blank">
            <span class="mx-1">
                {{ $user->last_known_username }}
            </span>
        </a>
    @endif

    @if($showAvatar)
        <a href="{{ $profileUrl }}" target="_blank" style="max-height: inherit">
            <img src="{{ $avatarFull }}"
                 alt="Avatar"
                 class="rounded-lg mx-1" style="max-height: inherit">
        </a>
    @endif
</div>