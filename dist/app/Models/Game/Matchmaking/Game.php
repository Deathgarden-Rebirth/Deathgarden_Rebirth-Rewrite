<?php

namespace App\Models\Game\Matchmaking;

use App\Enums\Game\Matchmaking\MatchStatus;
use App\Models\User\User;
use Cache;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin IdeHelperGame
 */
class Game extends Model
{
    const TRY_CREATE_MATCH_INTERVAL_SECONDS = 5;

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

    public static function tryToCreateMatch()
    {
        // skip when the lastz time this job run is not older than the interval seconds
        if(!(Cache::get('tryCreateMatch', 0) > time() - static::TRY_CREATE_MATCH_INTERVAL_SECONDS))
            return;

        Cache::set('tryCreateMatch', time());

    }
}
