<?php

namespace App\Http\Responses\Api\Player\Inbox;

use JsonSerializable;

class MessagePayload implements JsonSerializable
{
    public function __construct(
        public string $title,
        public string $body,
        public ?array $claimable = null,
    )
    {
    }

    public function jsonSerialize(): mixed
    {
        $data = [
            'title' => $this->title,
            'body' => $this->body,
        ];

        if($this->claimable !== null)
            $data['claimable'] = $this->claimable;

        return $data;
    }
}