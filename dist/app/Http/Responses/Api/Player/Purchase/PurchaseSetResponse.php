<?php

namespace App\Http\Responses\Api\Player\Purchase;

class PurchaseSetResponse
{

    public function __construct(
        public WalletEntry $cost,
        public WalletEntry $newBalance,
        public array $purchasedItems,
        public array $rewardItems,
    )
    {
    }
}