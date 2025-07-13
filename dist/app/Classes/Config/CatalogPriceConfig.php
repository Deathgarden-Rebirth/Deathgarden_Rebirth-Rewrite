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
            'CurrencyA' => 500,
            'CurrencyB' => 500,
        ],
        2 => [
            'CurrencyA' => 700,
            'CurrencyB' => 800,
        ],
        3 => [
            'CurrencyA' => 900,
            'CurrencyB' => 1200,
        ],
        4 => [
            'CurrencyA' => 1100,
            'CurrencyB' => 1600,
            'CurrencyC' => 400,
        ],
        5 => [
            'CurrencyA' => 1300,
            'CurrencyB' => 2000,
            'CurrencyC' => 1100,
        ],
        6 => [
            'CurrencyA' => 1500,
            'CurrencyB' => 2400,
            'CurrencyC' => 1700,
        ],
        7 => [
            'CurrencyA' => 1700,
            'CurrencyB' => 2900,
            'CurrencyC' => 2400,
        ],
        8 => [
            'CurrencyA' => 1900,
            'CurrencyB' => 3400,
            'CurrencyC' => 3100,
        ],
        9 => [
            'CurrencyA' => 2100,
            'CurrencyB' => 4000,
            'CurrencyC' => 3900,
        ],
        10 => [
            'CurrencyA' => 2300,
            'CurrencyB' => 4600,
            'CurrencyC' => 4700,
        ],
    ];

    const SKINS_COST = [
        ItemQuality::Basic->value => ['CurrencyA' => 800, 'CurrencyC'=> 1200],
        ItemQuality::Specialized->value => ['CurrencyA' => 1600, 'CurrencyC' => 1900],
        ItemQuality::Rare->value => ['CurrencyA' => 2400, 'CurrencyC' => 2600],
        ItemQuality::Superior->value => ['CurrencyA' => 3200, 'CurrencyC' => 3000],
        ItemQuality::Epic->value => ['CurrencyA' => 4000, 'CurrencyC' => 5400],
        ItemQuality::Ultra->value => ['CurrencyA' => 6000, 'CurrencyC' => 8500],
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
