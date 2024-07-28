<?php

namespace App\Models\Game\Moderation;

use App\Http\Requests\Api\Moderation\ReportPlayerInfoListEntry;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

/**
 * @mixin IdeHelperPlayerReport
 */
class PlayerReport extends Model
{
    protected $casts = [
        'player_infos' => 'array',
    ];

    public function reportedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    public function reportingUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporting_user_id');
    }

    public function playerInfos(): Collection {
        $collection = collect();

        foreach ($this->player_infos as $playerInfo) {
            $collection->add(ReportPlayerInfoListEntry::makeFromArray($playerInfo));
        }

        return $collection;
    }
}
