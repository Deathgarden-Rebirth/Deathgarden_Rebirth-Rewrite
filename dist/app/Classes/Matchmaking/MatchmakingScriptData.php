<?php

namespace App\Classes\Matchmaking;

use App\Models\Game\Matchmaking\QueuedPlayer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class MatchmakingScriptData implements \JsonSerializable
{
    const TIME_MULTIPLIERS = [
        1 => 1,
        2 => 0.9,
        3 => 0.8,
    ];

    public array $allowedRunnerGroupSizes = [4,5,6];

    protected array $runnerGroupSizes;

    protected array $runnerQueueTimes;

    protected array $hunterQueueTimes;

    /**
     * @param Collection<QueuedPlayer> $runnerGroups
     * @param Collection<QueuedPlayer> $hunters
     */
    public function __construct(
        public Collection $runnerGroups,
        public Collection $hunters,
    )
    {

    }

    protected function generateData(): void
    {
        $this->runnerGroupSizes = [];
        $this->runnerQueueTimes = [];
        $this->hunterQueueTimes = [];
        $now = Carbon::now();

        foreach ($this->runnerGroups as $runnerGroup) {
            $this->runnerGroupSizes[] = 1 + $runnerGroup->followingUsers()->count();
            $this->runnerQueueTimes[] = $runnerGroup->created_at->diffInSeconds($now);
        }

        foreach ($this->hunters as $hunter) {
            $this->hunterQueueTimes[] = $hunter->created_at->diffInSeconds($now);
        }
    }

    public function jsonSerialize(): mixed
    {
        return [
            'runnerGroupWeights' => $this->runnerGroupSizes,
            'runnerGroupQueueTimes' => $this->runnerQueueTimes,
            'hunterGroupQueueTimes' => $this->hunterQueueTimes,
            'groupTimeMultiplier' => self::TIME_MULTIPLIERS,
            'allowedGroupSizes' => $this->allowedRunnerGroupSizes,
            'timeUnit' => 'seconds',
        ];
    }
}