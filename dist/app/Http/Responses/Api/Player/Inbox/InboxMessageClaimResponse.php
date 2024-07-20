<?php

namespace App\Http\Responses\Api\Player\Inbox;

use JsonSerializable;

class InboxMessageClaimResponse implements JsonSerializable
{
    /** @var InboxMessageClaimedReward[] array  */
    public array $inventories = [];

    /** @var InboxMessageClaimedReward[] array  */
    public array $currencies = [];

    public function jsonSerialize(): array
    {
        return [
            'claimed' => [
                'inventories' => $this->inventories,
                'currencies' => $this->currencies,
            ]
        ];
    }
}