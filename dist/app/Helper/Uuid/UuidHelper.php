<?php

namespace App\Helper\Uuid;

use Illuminate\Support\Collection;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

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

    /**
     * @param Collection|array $collection
     * @return Collection|UuidInterface[]
     */
    public static function convertFromHexToUuidCollecton(Collection|array $collection, bool $toString = false): Collection {
        $new = [];

        foreach ($collection as $id) {
            if($toString)
                $new[] = Uuid::fromHexadecimal(new Hexadecimal($id))->toString();
            else
                $new[] = Uuid::fromHexadecimal(new Hexadecimal($id));
        }

        return collect($new);
    }
}