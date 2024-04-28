<?php

namespace App\Helper\AppHelper;

use Illuminate\Support\Facades\Storage;

class AppEnv
{
    public static function isInMaintenanceMode(): bool {
        return Storage::disk('local')->exists('.maintenance');
    }
}