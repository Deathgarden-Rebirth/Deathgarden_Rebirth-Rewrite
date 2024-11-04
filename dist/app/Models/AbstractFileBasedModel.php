<?php

namespace App\Models;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

abstract class AbstractFileBasedModel
{
    const FILE_NAME = 'file-name';

    const CACHE_DURATION = 3600;

    protected static ?FilesystemAdapter $disk = null;

    public function save(): bool|string {
        $success = static::getDisk()->put(static::FILE_NAME, serialize($this));

        if($success === true)
            Cache::forget(static::FILE_NAME);

        return $success;
    }

    public static function get(): ?static {
        return Cache::remember(
            static::FILE_NAME,
            static::CACHE_DURATION,
            function () {
                $data = static::getDisk()->get(static::FILE_NAME);

                return $data === null ? static::getDefault() : unserialize($data);
            }
        );
    }

    protected static function getDisk(): FilesystemAdapter {
        return static::$disk ?? static::$disk = Storage::disk('local');
    }

    protected static function getDefault(): ?static {
        return null;
    }
}