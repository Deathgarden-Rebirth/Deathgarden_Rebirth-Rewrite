<?php

namespace App\Models\Admin\Versioning;

use App\Models\AbstractFileBasedModel;

class LauncherVersion extends AbstractFileBasedModel
{
    const FILE_NAME = 'current-launcher-version';

    const CACHE_DURATION = 86400; // 1 Day

    public function __construct(
        public string $launcherVersion,
    )
    {}
}