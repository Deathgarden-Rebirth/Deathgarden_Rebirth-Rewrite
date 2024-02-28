<?php

namespace App\Models\Game;

use App\Models\User\PlayerData;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperChallenge
 */
class Challenge extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'completion_value',
        'asset_path',
    ];

    public function playerData()
    {
        return $this->belongsToMany(PlayerData::class)->withPivot(['progress']);
    }

    public function getProgressForPlayer(int $playerDataId): int
    {
        $foundProgress = $this->playerData()->where('id', '=', $playerDataId)->first();

        if ($foundProgress !== null)
            return $foundProgress->pivot->progress;

        // We create challange relation and just return 0 since you cannot have progress for the challenge we just linked.
        $this->playerData()->attach($playerDataId);
        return 0;
    }
}
