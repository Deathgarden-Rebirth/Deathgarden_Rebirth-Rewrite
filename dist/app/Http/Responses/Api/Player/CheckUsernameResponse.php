<?php

namespace App\Http\Responses\Api\Player;

class CheckUsernameResponse
{
    public function __construct(
        public string $UserId,
        public string $PlayerName,
    )
    {
    }
}