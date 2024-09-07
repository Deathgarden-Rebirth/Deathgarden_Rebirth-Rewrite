<?php

namespace App\Models\Admin\Versioning;

use App\Models\AbstractFileBasedModel;

class CurrentCatalogVersion extends AbstractFileBasedModel
{
    const FILE_NAME = 'current-catalog-version';

    const CACHE_DURATION = 86400; // 1 Day

    public function __construct(
        public string $catalogVersion
    )
    {}
}