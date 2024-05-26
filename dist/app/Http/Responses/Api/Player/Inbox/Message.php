<?php

namespace App\Http\Responses\Api\Player\Inbox;

class Message
{
    // Seems to be what they use as a ID Parameter
    public int $received;

    public string $flag;

    public MessagePayload $message;

    public ?string $tag = null;

    public ?int $expireAt = null;

    public ?string $origin = null;

    public string $recipientId;
}