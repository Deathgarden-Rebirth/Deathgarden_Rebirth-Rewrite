<?php

namespace App\View\Components\Admin;

use App\Models\Admin\Archive\ArchivedGame;
use App\Models\Admin\Archive\ArchivedPlayerProgression;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class MatchInfo extends Component
{
    public Collection $players;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public ArchivedGame $game,
    )
    {
        $this->players = ArchivedPlayerProgression::whereArchivedGameId($game->id)->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.match-info');
    }
}
