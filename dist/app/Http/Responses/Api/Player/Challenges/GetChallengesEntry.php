<?php

namespace App\Http\Responses\Api\Player\Challenges;

use App\Enums\Game\ChallengeType;
use App\Enums\Game\Faction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class GetChallengesEntry implements \JsonSerializable
{
    const ID_PREFIX = 'Timed:';

    public function __construct(
        public int $id,
        public Carbon        $startTime,
        public Carbon        $endTime,
        public ChallengeType $type,
        public int           $completionValue,
        public Faction       $faction,
        public string        $challengeBlueprint,
        public array         $rewards,
        public bool $claimed,
    )
    {}

    public function getChallengeId(): string {
        return static::ID_PREFIX . $this->id;
    }

    public function jsonSerialize(): mixed
    {
        $json = [
            'lifetime' =>
                [
                    'creationTime' => $this->startTime->toIso8601ZuluString(),
                    'expirationTime' => $this->endTime->toIso8601ZuluString(),
                ],
            'challengeType' => $this->type,
            'challengeId' => $this->getChallengeId(),
            'challengeCompletionValue' => $this->completionValue,
            'faction' => $this->faction,
            'challengeBlueprint' => $this->challengeBlueprint,
        ];

        $rewards = [];
        foreach ($this->rewards as &$reward) {
            $reward['claimed'] = $this->claimed;
            $reward['weight'] = 100;
            $rewards[] = $reward;
        }
        $json['rewards'] = $rewards;

        return $json;
    }
}