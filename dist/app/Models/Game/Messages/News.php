<?php

namespace App\Models\Game\Messages;

use App\Enums\Game\Message\GameNewsRedirectMode;
use App\Enums\Game\Message\MessageType;
use DateTime;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperNews
 */
class News extends Model
{
    use HasFactory, HasUuids;

    protected $casts = [
        'message_type' => MessageType::class,
        'redirect_mode' => GameNewsRedirectMode::class,
    ];
}
