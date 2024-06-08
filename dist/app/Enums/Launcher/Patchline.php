<?php

namespace App\Enums\Launcher;

enum Patchline: int
{
    case LIVE = 0;
    case DEV = 1;
    case PLAYTESTER = 2;

    public static function tryFromName(string $name): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }
        return null;
    }
}
