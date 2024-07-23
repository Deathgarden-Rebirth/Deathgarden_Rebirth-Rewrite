<?php

namespace App\Models\Admin;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperBadChatMessage
 */
class BadChatMessage extends Model
{

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
