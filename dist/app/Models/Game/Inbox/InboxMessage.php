<?php

namespace App\Models\Game\Inbox;

use App\Http\Responses\Api\Player\Inbox\Message;
use App\Http\Responses\Api\Player\Inbox\MessagePayload;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperInboxMessage
 */
class InboxMessage extends Model
{
    use HasFactory;

    protected $casts = [
        'claimable' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function toMessageResponse(): Message {
        $message = new Message();

        $message->received = $this->id;
        $message->flag = $this->flag;
        $message->message = new MessagePayload(
            $this->title,
            $this->body,
            $this->claimable
        );
        $message->tag = $this->tag;
        $message->expireAt = $this->expire_at;
        $message->origin = $this->origin;
        $message->recipientId = $this->user->id;

        return $message;
    }
}
