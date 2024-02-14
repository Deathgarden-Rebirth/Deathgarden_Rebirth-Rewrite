<?php

namespace App\Models\User;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Permission\Traits\HasRoles;

/**
 * @mixin \Eloquent
 * @mixin IdeHelperUser
 */
class User extends Model implements \Illuminate\Contracts\Auth\Authenticatable
{
    use HasUuids, Authenticatable, HasRoles;

    protected $fillable = [
        'steam_id',
    ];

    public function ban(): HasOne
    {
        return $this->hasOne(Ban::class);
    }

    public static function findBySteamID(int $steamId): User|null
    {
        return static::where('steam_id', $steamId)->first();
    }
}
