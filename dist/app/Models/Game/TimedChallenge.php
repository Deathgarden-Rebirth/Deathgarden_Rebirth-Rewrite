<?php

namespace App\Models\Game;

use App\Enums\Game\ChallengeType;
use App\Enums\Game\Faction;
use App\Enums\Game\RewardType;
use App\Http\Responses\Api\General\Reward;
use App\Models\User\PlayerData;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin IdeHelperTimedChallenge
 */
class TimedChallenge extends Model
{
    protected $casts = [
        'type' => ChallengeType::class,
        'faction' => Faction::class,
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'rewards' => 'array',
    ];

    public function getProgressForPlayer(int $playerDataId): int
    {
        $foundProgress = $this->playerData()->where('id', '=', $playerDataId)->first();

        if ($foundProgress !== null)
            return $foundProgress->pivot->progress;

        // We create challange relation and just return 0 since you cannot have progress for the challenge we just linked.
        $this->playerData()->attach($playerDataId);
        return 0;
    }

    public function hasPlayerClaimed(int $playerDataId): bool {
        $foundPlayerData = $this->playerData()->where('id', '=', $playerDataId)->first();

        if ($foundPlayerData !== null)
            return $foundPlayerData->pivot->claimed;

        return false;
    }

    /**
     * @return Reward[]
     */
    public function getRewards(): array {
        if ($this->rewards === null)
            return [];

        $result = [];
        foreach ($this->rewards as $reward) {
            $result[] = new Reward(
                RewardType::tryFrom($reward['type']),
                $reward['amount'],
                $reward['id'],
            );
        }

        return $result;
    }

    public function playerData(): BelongsToMany
    {
        return $this->belongsToMany(PlayerData::class)->withPivot(['progress', 'claimed']);
    }

    public static function currentChallenges(): Eloquent|Builder {
        return static::where('start_time', '>', Carbon::now())
            ->where('end_time', '<', Carbon::now());
    }
}
