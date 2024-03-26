<?php

namespace App\View\Components\Misc\Progression;

use App\Models\Game\CharacterData;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CharacterProgression extends Component
{
    public float $progress = 0;

    public int $neededExperience;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public CharacterData $character,
    )
    {
        $this->neededExperience = CharacterData::getExperienceForLevel($character->level);
        $this->progress = (float)$character->experience / (float)$this->neededExperience ;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.misc.progression.character-progression');
    }
}
