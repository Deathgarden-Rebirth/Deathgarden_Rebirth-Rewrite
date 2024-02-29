<?php

namespace App\Http\Responses\Api\Player\Challenges;

class ChallengeProgressionEntry implements \JsonSerializable
{
    public string $challengeId;

    public bool $completed;

    public int $value;

    public array $rewardsClaimed = [];

    protected int $schemaVersion = 1;

    protected string $className = 'ChallengeProgressionCounter';

    public function __construct(string $challengeId, bool $completed, int $progress)
    {
        $this->challengeId = $challengeId;
        $this->completed = $completed;
        $this->value = $progress;
    }

    public function jsonSerialize(): mixed
    {
        $result = [
            'challengeId' => $this->challengeId,
            'completed' => $this->completed,
        ];

        if($this->value === 0)
            return $result;
        else
            $result['value'] = $this->value;

        if(!$this->completed)
            return $result;

        $result['className'] = $this->className;
        $result['schemaVersion'] = $this->schemaVersion;
        $result['rewardsClaimed'] = $this->rewardsClaimed;

        return $result;
    }
}