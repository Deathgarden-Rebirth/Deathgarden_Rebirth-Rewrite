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
            'CurrencyA' => 800,
            'CurrencyB' => 500,
        ],
        2 => [
            'CurrencyA' => 1000,
            'CurrencyB' => 800,
        ],
        3 => [
            'CurrencyA' => 1200,
            'CurrencyB' => 1200,
        ],
        4 => [
            'CurrencyA' => 1400,
            'CurrencyB' => 1600,
            'CurrencyC' => 500,
        ],
        5 => [
            'CurrencyA' => 1800,
            'CurrencyB' => 2000,
            'CurrencyC' => 1500,
        ],
        6 => [
            'CurrencyA' => 2200,
            'CurrencyB' => 2500,
            'CurrencyC' => 2000,
        ],
        7 => [
            'CurrencyA' => 2600,
            'CurrencyB' => 3000,
            'CurrencyC' => 3000,
        ],
        8 => [
            'CurrencyA' => 3100,
            'CurrencyB' => 3500,
            'CurrencyC' => 4000,
        ],
        9 => [
            'CurrencyA' => 3700,
            'CurrencyB' => 4000,
            'CurrencyC' => 5000,
        ],
        10 => [
            'CurrencyA' => 4200,
            'CurrencyB' => 4500,
            'CurrencyC' => 6500,
        ],
    ];

    const SKINS_COST = [
        ItemQuality::Basic->value => ['CurrencyA' => 1250, 'CurrencyC'=> 2500],
        ItemQuality::Specialized->value => ['CurrencyA' => 2500, 'CurrencyC' => 3500],
        ItemQuality::Rare->value => ['CurrencyA' => 3250, 'CurrencyC' => 4500],
        ItemQuality::Superior->value => ['CurrencyA' => 4000, 'CurrencyC' => 5500],
        ItemQuality::Epic->value => ['CurrencyA' => 4500, 'CurrencyC' => 6250],
        ItemQuality::Ultra->value => ['CurrencyA' => 5000, 'CurrencyC' => 7500],
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
