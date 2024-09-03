<?php

namespace App\Classes\Config;

use App\Enums\Game\ItemQuality;

enum CatalogPriceCategory {
    case Weapon;
    case Vambrace;
    case Powers;
    case Perks;
    case Skins;
}

class CatalogPriceConfig
{
    const WEAPON_COSTS = [
        1 => [
            'CurrencyC' => 500,
        ],
        2 => [
            'CurrencyC' => 610,
        ],
        3 => [
            'CurrencyC' => 720,
            'CurrencyB' => 150,
        ],
        4 => [
            'CurrencyC' => 830,
            'CurrencyB' => 200,
        ],
        5 => [
            'CurrencyC' => 1000,
            'CurrencyB' => 250,
        ],
        6 => [
            'CurrencyC' => 1110,
            'CurrencyB' => 300,
        ],
        7 => [
            'CurrencyC' => 1220,
            'CurrencyB' => 350,
        ],
        8 => [
            'CurrencyC' => 1330,
            'CurrencyB' => 400,
        ],
        9 => [
            'CurrencyC' => 1440,
            'CurrencyB' => 450,
        ],
        10 => [
            'CurrencyC' => 1550,
            'CurrencyB' => 500,
        ],
    ];

    const VAMBRACE_COSTS = [
        1 => [
            'CurrencyC' => 500,
        ],
        2 => [
            'CurrencyC' => 610,
        ],
        3 => [
            'CurrencyC' => 720,
            'CurrencyB' => 150,
        ],
        4 => [
            'CurrencyC' => 830,
            'CurrencyB' => 200,
        ],
        5 => [
            'CurrencyC' => 1000,
            'CurrencyB' => 250,
        ],
        6 => [
            'CurrencyC' => 1110,
            'CurrencyB' => 300,
        ],
        7 => [
            'CurrencyC' => 1220,
            'CurrencyB' => 350,
        ],
        8 => [
            'CurrencyC' => 1330,
            'CurrencyB' => 400,
        ],
        9 => [
            'CurrencyC' => 1440,
            'CurrencyB' => 450,
        ],
        10 => [
            'CurrencyC' => 1550,
            'CurrencyB' => 500,
        ],
    ];

    const POWERS_COST = [
        1 => [
            'CurrencyC' => 500,
        ],
        2 => [
            'CurrencyC' => 610,
        ],
        3 => [
            'CurrencyC' => 720,
            'CurrencyB' => 150,
        ],
        4 => [
            'CurrencyC' => 830,
            'CurrencyB' => 200,
        ],
        5 => [
            'CurrencyC' => 1000,
            'CurrencyB' => 250,
        ],
        6 => [
            'CurrencyC' => 1110,
            'CurrencyB' => 300,
        ],
        7 => [
            'CurrencyC' => 1220,
            'CurrencyB' => 350,
        ],
        8 => [
            'CurrencyC' => 1330,
            'CurrencyB' => 400,
        ],
        9 => [
            'CurrencyC' => 1440,
            'CurrencyB' => 450,
        ],
        10 => [
            'CurrencyC' => 1550,
            'CurrencyB' => 500,
        ],
    ];

    const PERKS_COST = [
        1 => [
            'CurrencyB' => 450,
        ],
        2 => [
            'CurrencyB' => 560,
        ],
        3 => [
            'CurrencyA' => 100,
            'CurrencyB' => 670,
        ],
        4 => [
            'CurrencyA' => 150,
            'CurrencyB' => 780,
        ],
        5 => [
            'CurrencyA' => 200,
            'CurrencyB' => 890,
        ],
        6 => [
            'CurrencyA' => 250,
            'CurrencyB' => 1000,
        ],
        7 => [
            'CurrencyA' => 300,
            'CurrencyB' => 1110,
        ],
        8 => [
            'CurrencyA' => 350,
            'CurrencyB' => 1220,
        ],
        9 => [
            'CurrencyA' => 400,
            'CurrencyB' => 1330,
        ],
        10 => [
            'CurrencyA' => 450,
            'CurrencyB' => 1440,
            'CurrencyC' => 1000,
        ],
    ];

    const SKINS_COST = [
        ItemQuality::Basic->value => ['CurrencyB' => 1500],
        ItemQuality::Specialized->value => ['CurrencyB' => 3500],
        ItemQuality::Superior->value => ['CurrencyB' => 4500],
        ItemQuality::Epic->value => ['CurrencyC' => 5500],
    ];

    public static function GetCategoryPriceForLevel(CatalogPriceCategory $category, int|string $level): array {
        return match ($category) {
            CatalogPriceCategory::Weapon => static::convertConfigToCatalog(static::WEAPON_COSTS[$level]),
            CatalogPriceCategory::Vambrace => static::convertConfigToCatalog(static::VAMBRACE_COSTS[$level]),
            CatalogPriceCategory::Powers => static::convertConfigToCatalog(static::POWERS_COST[$level]),
            CatalogPriceCategory::Perks => static::convertConfigToCatalog(static::PERKS_COST[$level]),
            CatalogPriceCategory::Skins => static::convertConfigToCatalog(static::SKINS_COST[$level]),
        };
    }

    protected static function convertConfigToCatalog(array $config): array {
        $result = [];

        foreach ($config as $currencyId => $amount) {
            $result[] = [
                'currencyId' => $currencyId,
                'price' => $amount,
            ];
        }

        return $result;
    }
}
