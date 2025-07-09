<?php

namespace App\Models\Admin;

use App\Models\AbstractFileBasedModel;

class MatchmakingSettings extends AbstractFileBasedModel
{
    const FILE_NAME = 'matchmakingSettings';

    const CACHE_DURATION = 86400;

    /**
     * How long the matchmaking should wait when only one 1v4 or 1v5 could be made before actually making it.
     */
    public int $matchWaitingTime = 10;

    protected static function getDefault(): ?static
    {
        return new static();
    }
}