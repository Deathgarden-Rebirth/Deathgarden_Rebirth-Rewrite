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
            'CurrencyC' => 350,
        ],
        2 => [
            'CurrencyC' => 460,
        ],
        3 => [
            'CurrencyC' => 570,
        ],
        4 => [
            'CurrencyC' => 680,
        ],
        5 => [
            'CurrencyC' => 790,
        ],
        6 => [
            'CurrencyC' => 900,
        ],
        7 => [
            'CurrencyC' => 1010,
            'CurrencyA' => 220,
        ],
        8 => [
            'CurrencyC' => 1120,
            'CurrencyA' => 330,
        ],
        9 => [
            'CurrencyC' => 1230,
            'CurrencyA' => 440,
        ],
        10 => [
            'CurrencyC' => 1340,
            'CurrencyA' => 540,
        ],
    ];

    const VAMBRACE_COSTS = [
        1 => [
            'CurrencyC' => 350,
        ],
        2 => [
            'CurrencyC' => 460,
        ],
        3 => [
            'CurrencyC' => 570,
        ],
        4 => [
            'CurrencyC' => 680,
        ],
        5 => [
            'CurrencyC' => 790,
        ],
        6 => [
            'CurrencyC' => 900,
        ],
        7 => [
            'CurrencyC' => 1010,
            'CurrencyA' => 220,
        ],
        8 => [
            'CurrencyC' => 1120,
            'CurrencyA' => 330,
        ],
        9 => [
            'CurrencyC' => 1230,
            'CurrencyA' => 440,
        ],
        10 => [
            'CurrencyC' => 1340,
            'CurrencyA' => 540,
        ],
    ];

    const POWERS_COST = [
        1 => [
            'CurrencyC' => 350,
        ],
        2 => [
            'CurrencyC' => 460,
        ],
        3 => [
            'CurrencyC' => 570,
        ],
        4 => [
            'CurrencyC' => 680,
        ],
        5 => [
            'CurrencyC' => 790,
        ],
        6 => [
            'CurrencyC' => 900,
        ],
        7 => [
            'CurrencyC' => 1010,
            'CurrencyA' => 220,
        ],
        8 => [
            'CurrencyC' => 1120,
            'CurrencyA' => 330,
        ],
        9 => [
            'CurrencyC' => 1230,
            'CurrencyA' => 440,
        ],
        10 => [
            'CurrencyC' => 1340,
            'CurrencyA' => 540,
        ],
    ];

    const PERKS_COST = [
        1 => [
            'CurrencyA' => 450,
        ],
        2 => [
            'CurrencyA' => 560,
        ],
        3 => [
            'CurrencyA' => 670,
            'CurrencyB' => 310,
        ],
        4 => [
            'CurrencyA' => 780,
            'CurrencyB' => 420,
        ],
        5 => [
            'CurrencyA' => 890,
            'CurrencyB' => 530,
        ],
        6 => [
            'CurrencyA' => 1000,
            'CurrencyB' => 640,
        ],
        7 => [
            'CurrencyA' => 1110,
            'CurrencyB' => 750,
        ],
        8 => [
            'CurrencyA' => 1220,
            'CurrencyB' => 860,
        ],
        9 => [
            'CurrencyA' => 1330,
            'CurrencyB' => 970,
        ],
        10 => [
            'CurrencyA' => 1440,
            'CurrencyB' => 1080,
            'CurrencyC' => 700,
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