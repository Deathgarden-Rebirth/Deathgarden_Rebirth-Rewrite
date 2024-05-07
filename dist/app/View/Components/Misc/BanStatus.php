<?php

namespace App\View\Components\Misc;

use App\Enums\Api\Ban\BanStatus as BanStatusEnum;
use App\Models\User\User;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\View\Component;

class BanStatus extends Component
{
    public BanStatusEnum $banStatus;

    /**
     * Create a new component instance.
     */
    public function __construct(public User $user)
    {
        if(!$user->bans()->exists()) {
            $this->banStatus = BanStatusEnum::Good;
            return;
        }

        $banCheckQuery = $user->bans()
            ->whereDate('start_date', '<', Carbon::now())
            ->whereDate('end_date', '>', Carbon::now());

        if ($banCheckQuery->exists())
            $this->banStatus = BanStatusEnum::Banned;
        else
            $this->banStatus = BanStatusEnum::Warning;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.misc.ban-status');
    }
}
