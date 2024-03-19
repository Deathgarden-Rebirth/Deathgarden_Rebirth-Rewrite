<?php

namespace App\View\Components\Admin\Tools;

use App\Models\Game\Messages\News;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class GameNewsListEntry extends Component
{
    public string $idPrefix;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public News $news,
        ?string $idPrefix = null,
    )
    {
        $this->idPrefix = $idPrefix === null ? '' : $idPrefix.'_';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.tools.game-news-list-entry');
    }
}
