<?php

namespace App\Enums\Game;

enum RewardType: string
{
    case Currency = 'currency';
    case Inventory = 'inventory';
    case Progression = 'progression';
}
