<?php

namespace App\Enums\Game;

enum ItemQuality: string
{

    case Basic = 'Basic';
    case Specialized = 'Specialized';
    case Superior = 'Superior';
    case Epic = 'Epic';
    case Legendary = 'Legendary';
    case Legacy = 'Legacy';
    case Prestige = 'Prestige';

    // Not used in catalog, but in game struct
    case Standard = 'Standart';
    case Rare = 'Rare';
    case Ultra = 'Ultra';
}
