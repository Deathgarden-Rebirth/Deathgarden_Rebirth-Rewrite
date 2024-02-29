<?php

namespace App\Enums\Game;

enum RewardType: string
{
    case Currency = 'Currency';
    case Inventory = 'Inventory';
    case Progression = 'Progression';
}
