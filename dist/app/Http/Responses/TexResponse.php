<?php

namespace App\Http\Responses;

use App\Http\Controllers\Api\VersionController;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use JsonSerializable;

class TexResponse implements JsonSerializable
{
    public string $id = 'tex';

    public string $name = 'tex';

    public string $description = 'The Exit Rebirth Rewrite';

    public string $url;

    public ?array $list = null;

    public Event $currentEvent;

    public function __construct()
    {
        $this->url = route(VersionController::ROUTE_TEX);
        $this->currentEvent = new Event();
    }

    public function jsonSerialize(): mixed
    {
        $result = [];
        foreach ($this as $name => $attribute) {
            if ($name === 'currentEvent')
                continue;
            $result[$name] = $attribute;
        }

        $result['current-event'] = $this->currentEvent;

        return $result;
    }
}

class Event {

    public string $url = '';

    public int $timestamp;

    public string $sid = 'ahdzfnB1Ymxpc2hpbmctc3Rhc2hib2FyZHISCxIFRXZlbnQYgICAgMC1mwoM';

    public string $message = 'up';

    public bool $informational = false;

    public EventStatus $status;

    public function __construct()
    {
        $this->timestamp = Carbon::now()->getTimestamp();

        $this->status = new EventStatus();
        $this->status->url = $this->url;
    }
}

class EventStatus {

    public string $description = 'The service is up';

    public string $level = 'NORMAL';

    public bool $default = true;

    public string $image = '';

    public string $url = '';

    public string $id = 'up';

    public string $name = 'Up';
}