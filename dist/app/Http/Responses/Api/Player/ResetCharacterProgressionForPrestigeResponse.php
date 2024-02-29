<?php

namespace App\Http\Responses\Api\Player;

use App\Http\Responses\Api\General\Reward;

class ResetCharacterProgressionForPrestigeResponse
{
    public bool $isCharacterItemReset = true;
    public bool $isCharacterChallengesReset = true;
    public bool $isCharacterExperienceReset = true;

    public int $prestigelevel;

    /** @var Reward[] */
    public array $rewards;

    public static function createAbortResponse(int $prestigeLevel): ResetCharacterProgressionForPrestigeResponse
    {
        $response = new ResetCharacterProgressionForPrestigeResponse();
        $response->isCharacterExperienceReset = false;
        $response->isCharacterItemReset = false;
        $response->isCharacterChallengesReset = false;
        $response->prestigelevel = $prestigeLevel;
        $response->rewards = [];

        return $response;
    }
}