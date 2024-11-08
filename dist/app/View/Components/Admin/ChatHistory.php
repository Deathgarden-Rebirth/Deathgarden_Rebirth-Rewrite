<?php

namespace App\View\Components\Admin;

use App\Classes\Frontend\ChatMessage;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ChatHistory extends Component
{
    /**
     * @param ChatMessage[] $messages
     */
    public function __construct(
        public array $messages
    )
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.chat-history');
    }
}
