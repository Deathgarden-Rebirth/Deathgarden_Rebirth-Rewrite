<?php

namespace App\Classes\Config;

use App\Enums\Game\ItemQuality;

enum CatalogPriceCategory
{
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
            'CurrencyC' => 750,
        ],
        3 => [
            'CurrencyC' => 1000,
        ],
        4 => [
            'CurrencyC' => 1250,
        ],
        5 => [
            'CurrencyC' => 1500,
        ],
        6 => [
            'CurrencyA' => 425,
            'CurrencyC' => 1300,
        ],
        7 => [
            'CurrencyA' => 525,
            'CurrencyC' => 1400,
        ],
        8 => [
            'CurrencyA' => 625,
            'CurrencyC' => 1500,
        ],
        9 => [
            'CurrencyA' => 725,
            'CurrencyC' => 1600,
        ],
        10 => [
            'CurrencyA' => 825,
            'CurrencyC' => 1700,
        ],
    ];

    const VAMBRACE_COSTS = [
        1 => [
            'CurrencyC' => 500,
        ],
        2 => [
            'CurrencyC' => 750,
        ],
        3 => [
            'CurrencyC' => 1000,
        ],
        4 => [
            'CurrencyC' => 1250,
        ],
        5 => [
            'CurrencyC' => 1500,
        ],
        6 => [
            'CurrencyA' => 425,
            'CurrencyC' => 1300,
        ],
        7 => [
            'CurrencyA' => 525,
            'CurrencyC' => 1400,
        ],
        8 => [
            'CurrencyA' => 625,
            'CurrencyC' => 1500,
        ],
        9 => [
            'CurrencyA' => 725,
            'CurrencyC' => 1600,
        ],
        10 => [
            'CurrencyA' => 825,
            'CurrencyC' => 1700,
        ],
    ];

    const POWERS_COST = [
        1 => [
            'CurrencyC' => 500,
        ],
        2 => [
            'CurrencyC' => 750,
        ],
        3 => [
            'CurrencyC' => 1000,
        ],
        4 => [
            'CurrencyC' => 1250,
        ],
        5 => [
            'CurrencyC' => 1500,
        ],
        6 => [
            'CurrencyA' => 425,
            'CurrencyC' => 1300,
        ],
        7 => [
            'CurrencyA' => 525,
            'CurrencyC' => 1400,
        ],
        8 => [
            'CurrencyA' => 625,
            'CurrencyC' => 1500,
        ],
        9 => [
            'CurrencyA' => 725,
            'CurrencyC' => 1600,
        ],
        10 => [
            'CurrencyA' => 825,
            'CurrencyC' => 1700,
        ],
    ];

    const PERKS_COST = [
        1 => [
            'CurrencyA' => 600,
            'CurrencyB' => 700,
        ],
        2 => [
            'CurrencyA' => 800,
            'CurrencyB' => 1000,
        ],
        3 => [
            'CurrencyA' => 1000,
            'CurrencyB' => 1400,
        ],
        4 => [
            'CurrencyA' => 1200,
            'CurrencyB' => 1700,
            'CurrencyC' => 600,
        ],
        5 => [
            'CurrencyA' => 1400,
            'CurrencyB' => 2100,
            'CurrencyC' => 1600,
        ],
        6 => [
            'CurrencyA' => 1600,
            'CurrencyB' => 2600,
            'CurrencyC' => 2100,
        ],
        7 => [
            'CurrencyA' => 1800,
            'CurrencyB' => 3100,
            'CurrencyC' => 3100,
        ],
        8 => [
            'CurrencyA' => 2000,
            'CurrencyB' => 3600,
            'CurrencyC' => 4100,
        ],
        9 => [
            'CurrencyA' => 2200,
            'CurrencyB' => 4200,
            'CurrencyC' => 5000,
        ],
        10 => [
            'CurrencyA' => 2500,
            'CurrencyB' => 5000,
            'CurrencyC' => 6000,
        ],
    ];

    const SKINS_COST = [
        ItemQuality::Basic->value => ['CurrencyA' => 500, 'CurrencyC'=> 3250],
        ItemQuality::Specialized->value => ['CurrencyA' => 1250, 'CurrencyC' => 4500],
        ItemQuality::Rare->value => ['CurrencyA' => 2000, 'CurrencyC' => 5000],
        ItemQuality::Superior->value => ['CurrencyA' => 2500, 'CurrencyC' => 5500],
        ItemQuality::Epic->value => ['CurrencyA' => 3000, 'CurrencyC' => 6500],
        ItemQuality::Ultra->value => ['CurrencyA' => 4500, 'CurrencyC' => 7500],
    ];

    public static function GetCategoryPriceForLevel(CatalogPriceCategory $category, int|string $level): array
    {
        return match ($category) {
            CatalogPriceCategory::Weapon => static::convertConfigToCatalog(static::WEAPON_COSTS[$level]),
            CatalogPriceCategory::Vambrace => static::convertConfigToCatalog(static::VAMBRACE_COSTS[$level]),
            CatalogPriceCategory::Powers => static::convertConfigToCatalog(static::POWERS_COST[$level]),
            CatalogPriceCategory::Perks => static::convertConfigToCatalog(static::PERKS_COST[$level]),
            CatalogPriceCategory::Skins => static::convertConfigToCatalog(static::SKINS_COST[$level]),
        };
    }

    protected static function convertConfigToCatalog(array $config): array
    {
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
