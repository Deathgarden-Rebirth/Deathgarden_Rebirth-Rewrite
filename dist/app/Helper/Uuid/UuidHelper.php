<?php

namespace App\Helper\Uuid;

use Illuminate\Support\Collection;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Uuid;

abstract class UuidHelper
{
    public static function convertFromUuidToHexCollection(Collection|array $collection, bool $uppercase = true): Collection {
        $new = [];

        foreach ($collection as $id) {
            if($uppercase)
                $new[] = strtoupper(Uuid::fromString($id)->getHex()->toString());
            else
                $new[] = Uuid::fromString($id)->getHex()->toString();
        }

        return collect($new);
    }

    public static function convertFromHexToUuidCollecton(Collection|array $collection): Collection {
        $new = [];

        foreach ($collection as $id) {
            $new[] = Uuid::fromHexadecimal(new Hexadecimal($id));
        }

        return collect($new);
    }
}