<?php

namespace App\Models\Game\Inbox;

use App\Http\Responses\Api\Player\Inbox\InboxMessageReward;
use App\Http\Responses\Api\Player\Inbox\Message;
use App\Http\Responses\Api\Player\Inbox\MessagePayload;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperInboxMessage
 */
class InboxMessage extends Model
{
    use HasFactory, SoftDeletes, Prunable;

    protected $casts = [
        'received' => 'datetime',
        'claimable' => 'array',
        'expire_at' => 'datetime',
    ];

    protected $attributes = [
        'claimable' => [],
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function toMessageResponse(): Message {
        $message = new Message();

        $message->received = $this->received->getTimestampMs();
        $message->flag = $this->flag;
        $message->message = new MessagePayload(
            $this->title,
            $this->body,
            $this->claimable,
            $this->has_claimed,
        );
        $message->tag = $this->tag;
        $message->expireAt = $this->expire_at?->getTimestamp();
        $message->origin = $this->origin;
        $message->recipientId = $this->user->id;
        $message->receivedTimestamp = $this->created_at->getTimestamp();

        return $message;
    }

    /**
     * @return InboxMessageReward[]
     */
    public function getClaimables(): array {
        if($this->claimable === null)
            return [];

        $result = [];
        foreach ($this->claimable as $claimable) {
            $result[] = new InboxMessageReward(
                $claimable['type'],
                $claimable['amount'],
                $claimable['id'],
            );
        }

        return $result;
    }

    public function setClaimables(array $claimables): void {
        $newClaimables = [];

        foreach ($claimables as $claimable) {
            $newClaimables[] = (array)$claimable;
        }

        $this->claimable = $newClaimables;
    }

    public function prunable(): Builder|InboxMessage
    {
        // Mark Expired Messages as Deleted
        static::where('expire_at', '<', Carbon::now())->delete();

        // Delete Models that were soft-deleted a week ago.
        return static::where('deleted_at', '<=', Carbon::now()->subWeek());
    }
}
