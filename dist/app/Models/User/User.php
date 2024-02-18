<?php

namespace App\Models\User;

use App\Models\PlayerData;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as AuthUser;
use Spatie\Permission\Traits\HasRoles;

/**
 * @mixin \Eloquent
 * @mixin IdeHelperUser
 */
class User extends AuthUser
{
    use HasUuids, HasRoles;

    protected $fillable = [
        'steam_id',
        'source',
        'last_known_username',
    ];

    public function ban(): HasOne
    {
        return $this->hasOne(Ban::class);
    }

    public function playerData(): PlayerData|Model
    {
        return $this->hasOne(PlayerData::class)->firstOrCreate();
    }

    public static function findBySteamID(int $steamId): User|null
    {
        return static::where('steam_id', $steamId)->first();
    }
}
