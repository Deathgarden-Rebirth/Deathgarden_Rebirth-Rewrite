<?php

namespace App\Http\Responses\Api\Player\Inbox;

use App\Models\Game\CatalogItem;
use Ramsey\Uuid\Uuid;

class InboxMessageReward
{
    public function __construct(
        public string $type,
        public int $amount,
        public string $id,
    )
    {}

    public function getRewardName(): string {
        if ($this->type === 'Currency') {
            return match ($this->id) {
                'CurrencyA' => 'Currency A',
                'CurrencyB' => 'Currency B',
                'CurrencyC' => 'Currency C',
                default => 'Invalid Currency'
            };
        }

        $item = CatalogItem::find(Uuid::fromString($this->id)->toString());

        return $item === null ? 'Invalid Item' : $item->display_name;
    }
}