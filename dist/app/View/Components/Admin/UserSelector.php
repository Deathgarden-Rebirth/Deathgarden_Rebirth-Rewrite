<?php

namespace App\View\Components\Admin;

use App\Models\User\User;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class UserSelector extends Component
{
    public array $prefillData = [];

    /**
     * @param User[] $prefillUsers
     */
    public function __construct(array|Collection $prefillUsers = [])
    {
        foreach ($prefillUsers as $prefillUser) {
            $this->prefillData[] = [
                'id' => $prefillUser->id,
                'text' => $prefillUser->last_known_username ?? $prefillUser->id,
                'selected' => true,
            ];
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.user-selector');
    }
}
