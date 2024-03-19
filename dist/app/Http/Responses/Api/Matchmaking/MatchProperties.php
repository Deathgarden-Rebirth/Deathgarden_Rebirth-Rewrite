<?php

namespace App\Http\Responses\Api\Matchmaking;

use App\Models\Game\Matchmaking\MatchConfiguration;

class MatchProperties implements \JsonSerializable
{
    public MatchConfiguration $matchConfiguration;

    protected string $platform = 'Windows';

    public function __construct(MatchConfiguration $matchConfiguration)
    {
        $this->matchConfiguration = $matchConfiguration;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'matchConfiguration' => $this->matchConfiguration->asset_path,
            'countA' => $this->matchConfiguration->hunters,
            'countB' => $this->matchConfiguration->runners,
            'gameMode' => md5($this->matchConfiguration->asset_path) . '-Default',
            'platform' => $this->platform,
        ];
    }
}