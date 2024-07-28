<?php

namespace App\Http\Responses\Api\Player\Inbox;

class InboxMessageClaimedReward
{
    public function __construct(
        public string $id,
        public int $newAmount,
        public int $receivedAmount,
    )
    {}
}