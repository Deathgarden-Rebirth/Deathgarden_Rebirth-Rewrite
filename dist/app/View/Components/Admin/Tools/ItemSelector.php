<?php

namespace App\View\Components\Admin\Tools;

use App\Http\Responses\Api\Player\Inbox\InboxMessageReward;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ItemSelector extends Component
{
    /** @var InboxMessageReward[]  */
    public array $rewards;

    /**
     * Create a new component instance.
     */
    public function __construct(array $rewards)
    {
        $this->rewards = $rewards;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.tools.item-selector');
    }
}
