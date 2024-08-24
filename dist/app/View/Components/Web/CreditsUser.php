<?php

namespace App\View\Components\Web;

use App\Models\User\User;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CreditsUser extends Component
{
    public ?User $user = null;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public int $userSteamId,
        public string $headline = '',
        public ?string $usernameOverride = null,
        public ?string $avatarOverride = null,
    )
    {
        $this->user = User::whereSteamId($this->userSteamId)->first(['last_known_username', 'avatar_medium']);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.web.credits-user');
    }
}
