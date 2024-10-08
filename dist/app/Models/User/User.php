<?php

namespace App\Models\User;

use App\Enums\Game\Matchmaking\MatchStatus;
use App\Models\Game\Inbox\InboxMessage;
use App\Models\Game\Matchmaking\Game;
use Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Traits\HasRoles;

/**
 * @mixin \Eloquent
 * @mixin IdeHelperUser
 */
class User extends AuthUser
{
    use HasUuids, HasRoles;

    const PLAYER_DATA_LOCK = 'playerData';

    protected $fillable = [
        'steam_id',
        'source',
        'last_known_username',
    ];

    public function bans(): HasMany
    {
        return $this->hasMany(Ban::class);
    }

    public function playerData(): PlayerData|Model
    {
        // Shared Lock alone didn't prevent sometimes duplicate playerData entries.
        // Hopefully this lock will work.
        $lock = Cache::lock(static::PLAYER_DATA_LOCK . $this->id, 5);

        try {
            $lock->block(5);
            $playerData = $this->hasOne(PlayerData::class)->sharedLock()->firstOrCreate();
            $lock->release();
            return $playerData;
        } catch (LockTimeoutException $e) {
            Log::channel('daily')->emergency('Could not Acquire Player data lock, this should not happen.');
        }

        return $this->hasOne(PlayerData::class)->sharedLock()->firstOrCreate();
    }

    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class)->withPivot('side')->withTimestamps();
    }

    public function activeGames(): BelongsToMany
    {
        return $this->belongsToMany(Game::class)
            ->withPivot('side')
            ->whereIn('status', [MatchStatus::Created->value, MatchStatus::Opened->value])->withTimestamps();
    }

    public function inboxMessages(): HasMany
    {
        return $this->hasMany(InboxMessage::class);
    }

    public static function findBySteamID(int $steamId): User|null
    {
        return static::where('steam_id', $steamId)->first();
    }

}
