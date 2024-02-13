<?php

namespace App\Models\User;

use App\Models\IdeHelperUser;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @mixin IdeHelperUser
 */
class User extends Model implements \Illuminate\Contracts\Auth\Authenticatable
{
    use HasUuids, Authenticatable;

    protected $fillable = [
        'steam_id',
    ];

    public function ban(): HasOne
    {
        return $this->hasOne(Ban::class);
    }
}
