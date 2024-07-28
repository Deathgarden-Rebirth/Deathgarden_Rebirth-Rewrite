<?php

namespace App\View\Components\Admin\Tools;

use App\Models\Admin\BadChatMessage;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ChatMessageEntry extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public BadChatMessage $message)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.tools.chat-message-entry');
    }
}
