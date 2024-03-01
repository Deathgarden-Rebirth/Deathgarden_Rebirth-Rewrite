<?php

namespace App\Http\Responses\Api\Player;

class UpdateMetadataResponse
{
    public int $schemaVarsion = 1;

    public array $data = [];

    public function __construct(
        public string $userId,
        public \App\Enums\Game\MetadataGroup $objectId,
        public int $version,
        public string $stateName = 'MetadataGroups',
    )
    {
    }
}