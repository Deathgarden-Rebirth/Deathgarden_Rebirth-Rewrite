<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

/**
 * @mixin IdeHelperUser
 */
class User extends Model
{
    use HasUuids;

    protected $fillable = [
        'steam_id',
    ];
}
