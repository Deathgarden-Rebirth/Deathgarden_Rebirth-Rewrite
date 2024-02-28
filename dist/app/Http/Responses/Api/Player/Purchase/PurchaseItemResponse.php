<?php

namespace App\Http\Responses\Api\Player\Purchase;

class PurchaseItemResponse
{
    public function __construct(
        public string $playerId,
        public string $objectId,
        public int $quantity,
        public WalletEntry $walletEntry,
        public array $rewardItems = [],
    )
    {

    }
}