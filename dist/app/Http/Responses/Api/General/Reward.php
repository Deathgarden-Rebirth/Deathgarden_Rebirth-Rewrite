<?php

namespace App\Http\Responses\Api\General;

use App\Enums\Game\RewardType;

// Based on MirrorsModelReward - PDB v0.8.0
class Reward
{
    public function __construct(
        public RewardType $type,
        public int $amount,
        public string $id,
    )
    {}
}