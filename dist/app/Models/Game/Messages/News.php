<?php

namespace App\Models\Game\Messages;

use App\Enums\Game\Faction;
use App\Enums\Game\Message\GameNewsRedirectMode;
use App\Enums\Game\Message\MessageType;
use DateTime;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @mixin IdeHelperNews
 */
class News extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'uuid';

    protected $casts = [
        'message_type' => MessageType::class,
        'redirect_mode' => GameNewsRedirectMode::class,
        'faction' => Faction::class,
        'from_date' => 'datetime:Y-m-d H:i:s',
        'to_date' => 'datetime:Y-m-d H:i:s',
    ];
}
