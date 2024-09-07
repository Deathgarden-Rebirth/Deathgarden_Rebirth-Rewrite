<?php

namespace App\View\Components\Admin\Tools;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class InboxMessage extends Component
{
    public function __construct(
        public \App\Models\Game\Inbox\InboxMessage $message,
        public bool $allowEdit = false,
        public ?string $idPrefix = null,
    )
    {}

    public function render(): View|Closure|string
    {
        return view('components.admin.tools.inbox-message');
    }
}
