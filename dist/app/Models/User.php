<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

/**
 * @mixin IdeHelperUser
 */
class User extends Model implements \Illuminate\Contracts\Auth\Authenticatable
{
    use HasUuids, Authenticatable;

    protected $fillable = [
        'steam_id',
    ];
}
