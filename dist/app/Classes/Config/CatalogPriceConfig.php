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
            'CurrencyB' => 500,
        ],
        2 => [
            'CurrencyB' => 750,
        ],
        3 => [
            'CurrencyB' => 1000,
        ],
        4 => [
            'CurrencyB' => 1250,
        ],
        5 => [
            'CurrencyB' => 1500,
        ],
        6 => [
            'CurrencyA' => 425,
            'CurrencyB' => 1300,
        ],
        7 => [
            'CurrencyA' => 525,
            'CurrencyB' => 1400,
        ],
        8 => [
            'CurrencyA' => 625,
            'CurrencyB' => 1500,
        ],
        9 => [
            'CurrencyA' => 725,
            'CurrencyB' => 1600,
        ],
        10 => [
            'CurrencyA' => 825,
            'CurrencyB' => 1700,
        ],
    ];

    const SKINS_COST = [
        ItemQuality::Basic->value => ['CurrencyC' => 2500],
        ItemQuality::Specialized->value => ['CurrencyC' => 3500],
        ItemQuality::Rare->value => ['CurrencyC' => 4500],
        ItemQuality::Superior->value => ['CurrencyC' => 5500],
        ItemQuality::Epic->value => ['CurrencyC' => 6250],
        ItemQuality::Ultra->value => ['CurrencyC' => 7500],
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
