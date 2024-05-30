<?php

namespace App\View\Components\misc;

use App\Models\User\User;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class banStatus extends Component
{
    public $banStatus;

    /**
     * Create a new component instance.
     */
    public function __construct(public User $user)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.misc.ban-status');
    }
}
