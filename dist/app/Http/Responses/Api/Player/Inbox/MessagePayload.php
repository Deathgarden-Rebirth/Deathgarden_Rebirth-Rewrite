<?php

namespace App\Http\Responses\Api\Player\Inbox;

use JsonSerializable;

class MessagePayload implements JsonSerializable
{
    public string $title;

    public string $body;
    public array $claimable;
    public bool $hasClaimed;

    public function __construct(
        string $title,
        string $body,
        array  $claimable = [],
        bool   $hasClaimed = false,
    )
    {
        $this->title = $title;
        $this->body = $body;
        $this->claimable = $claimable;
        $this->hasClaimed = $hasClaimed;
    }

    public function jsonSerialize(): mixed
    {
        $data = [
            'title' => $this->title,
            'body' => $this->body,
        ];

        if ($this->claimable !== null)
            $data['claimable'] = [
                'data' => $this->claimable,
                'state' => $this->hasClaimed ? 'CLAIMED' : 'NONE',
            ];

        return $data;
    }
}