<?php

namespace App\View\Components\Auth;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class User extends Component
{
    public string $profileUrl;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public \App\Models\User\User $user,
        public bool $showAvatar = true,
        public bool $showName = true,
        public bool $reverseOrder = false,
    )
    {
        $this->profileUrl = 'https://steamcommunity.com/profiles/'.$user->steam_id;
    }

    public function avatarFull(): string
    {
        return $this->user->avatar_full ?? $this->user->avatar_medium ?? $this->user->avatar_small;
    }

    public function avatarMedium(): string
    {
        return $this->user->avatar_medium ?? $this->user->avatar_small ?? $this->user->avatar_full ?? '';
    }

    public function avatarSmall(): string {
        return $this->user->avatar_small ?? $this->user->avatar_medium ?? $this->user->avatar_full ?? '';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.auth.user');
    }
}
