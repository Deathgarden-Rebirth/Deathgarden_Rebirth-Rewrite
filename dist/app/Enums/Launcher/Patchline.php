<?php

namespace App\Enums\Launcher;

use App\Enums\Auth\Roles;

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

    public function getNeededRole(): ?Roles {
        return match($this) {
            self::LIVE => null,
            self::DEV => Roles::Admin,
            self::PLAYTESTER => Roles::Playtester,
        };
    }
}
