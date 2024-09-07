<?php

namespace App\Models\Admin\Versioning;

use App\Models\AbstractFileBasedModel;

class CurrentGameVersion extends AbstractFileBasedModel
{
    const FILE_NAME = 'current-game-version';

    const CACHE_DURATION = 86400; // 1 Day

    public function __construct(
        public string $gameVersion,
    )
    {}
}