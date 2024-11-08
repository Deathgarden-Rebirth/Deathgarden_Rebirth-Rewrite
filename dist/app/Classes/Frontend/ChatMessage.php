<?php

namespace App\Classes\Frontend;

use App\View\Components\Auth\User;
use Illuminate\Support\Carbon;
use JsonSerializable;

class ChatMessage implements JsonSerializable
{
    public function __construct(
        public string $gameId,
        public string $userId,
        public Carbon $messageTime,
        public string $message,
    )
    {}

    public function getUser(): ?\App\Models\User\User
    {
        return \App\Models\User\User::find($this->userId);
    }

    public function jsonSerialize(): array
    {
        return [
            'gameId' => $this->gameId,
            'userId' => $this->userId,
            'messageTime' => $this->messageTime->toJSON(),
            'message' => $this->message,
        ];
    }
}