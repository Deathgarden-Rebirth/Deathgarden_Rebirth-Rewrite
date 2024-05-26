<?php

namespace App\Http\Responses\Api\Player\Inbox;

class InboxMessageReward
{
    public function __construct(
        public string $rewardType,
        public int $amount,
        public string $id,
    )
    {}
}