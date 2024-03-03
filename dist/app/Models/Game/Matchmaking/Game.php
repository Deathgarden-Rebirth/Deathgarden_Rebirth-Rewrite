<?php

namespace App\Models\Game\Matchmaking;

use App\Classes\Matchmaking\MatchmakingPlayerCount;
use App\Enums\Game\Matchmaking\MatchmakingSide;
use App\Enums\Game\Matchmaking\MatchStatus;
use App\Models\User\User;
use Cache;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

/**
 * @mixin IdeHelperGame
 */
class Game extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'status',
        'creator_user_id',
    ];

    protected $casts = [
        'status' => MatchStatus::class,
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_user_id');
    }

    public function matchConfiguration(): BelongsTo
    {
        return $this->belongsTo(MatchConfiguration::class);
    }

    public function players(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('side');
    }

    public function addQueuedPlayer(QueuedPlayer $player): void
    {
        DB::transaction(function () use ($player){
            $this->players()->attach($player->user, ['side' => $player->side->value]);

            foreach ($player->followingUsers as $followingUser) {
                $this->players()->attach($followingUser->id, ['side', $followingUser->side->value]);
                $followingUser->delete();
            }

            $player->delete();
        });
    }

    public function determineHost(): void
    {
        $hunter = $this->players()->firstWhere('side', '=', MatchmakingSide::Hunter->value);
        $this->creator()->associate($hunter);
        $this->save();
    }

    public function remainingPlayerCount(): MatchmakingPlayerCount
    {
        $players = $this->players;
        $currentHunterCount = 0;
        $currentRunnerCount = 0;

        foreach ($players as $player) {
            if($player->pivot->side === MatchmakingSide::Hunter->value)
                ++$currentHunterCount;
            else
                ++$currentRunnerCount;
        }

        $config = $this->matchConfiguration;

        if($config === null)
            return new MatchmakingPlayerCount();

        return new MatchmakingPlayerCount(
            $config->hunters - $currentHunterCount,
            $config->runners - $currentRunnerCount,
        );
    }
}
