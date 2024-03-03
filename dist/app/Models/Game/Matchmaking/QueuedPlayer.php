<?php

namespace App\Models\Game\Matchmaking;

use App\Enums\Game\Matchmaking\MatchmakingSide;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperQueuedPlayer
 */
class QueuedPlayer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'side',
    ];

    protected $casts = [
        'side' => MatchmakingSide::class,
    ];

    protected static function boot()
    {
        parent::boot();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function followingUsers()
    {
        return $this->hasMany(QueuedPlayer::class, 'queued_player_id');
    }

    public function leader()
    {
        return $this->belongsTo(QueuedPlayer::class, 'queued_player_id');
    }
}
