<?php

namespace App\Models\Admin;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class LauncherMessage
{
    const FILE_NAME = 'launcherMessage';

    protected static ?FilesystemAdapter $disk = null;

    public function __construct(
        public string $message,
        public ?string $url,
    )
    {}

    public function saveMessage(): bool|string
    {
        $success = static::getDisk()->put(static::FILE_NAME, serialize($this));

        if($success === true)
            Cache::forget(static::FILE_NAME);

        return $success;
    }

    public static function getMessage(): ?static {
        return Cache::remember(
            static::FILE_NAME,
            3600,
            function () {
                $data = static::getDisk()->get(static::FILE_NAME);

                return $data === null ? null : unserialize($data);
            }
        );
    }

    protected static function getDisk(): FilesystemAdapter {
        return static::$disk ?? static::$disk = Storage::disk('local');
    }
}