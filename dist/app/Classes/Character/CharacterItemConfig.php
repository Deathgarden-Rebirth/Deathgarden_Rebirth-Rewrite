<?php

namespace App\Classes\Character;

abstract class CharacterItemConfig
{
    protected static array $defaultEquippedPerks;

    protected static array $defaultEquippedWeapons;

    protected static array $defaultEquipment;

    protected static array $defaultEquippedBonuses;

    /**
     * additional perks for Runners and Equipped Powers for Hunters
     *
     * @var array
     */
    protected static array $defaultAdditionalPerks;

    protected static array $defaultAdditionalWeapons;

    public static function getDefaultEquippedPerks(): array {
        return static::$defaultEquippedPerks;
    }

    public static function getDefaultEquippedWeapons(): array {
        return static::$defaultEquippedWeapons;
    }

    public static function getDefaultEquipment(): array {
        return static::$defaultEquipment;
    }

    public static function getDefaultEquippedBonuses(): array {
        return static::$defaultEquippedBonuses;
    }

    public static function getDefaultAdditionalPerks(): array {
        return static::$defaultAdditionalPerks;
    }

    public static function getDefaultEqippedPowers()
    {
        return static::$defaultAdditionalPerks;
    }

    public static function getDefaultAdditionalWeapons(): array {
        return static::$defaultAdditionalWeapons;
    }
}