<?php

namespace App\Models\Admin\Archive;

use App\Enums\Game\Faction;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperArchivedGame
 */
class ArchivedGame extends Model
{
    use HasUuids;

    protected $casts = [
        'dominant_faction' => Faction::class,
        'chat_messages' => 'array',
    ];

    public function archivedPlayerProgressions(): HasMany
    {
        return $this->hasMany(ArchivedPlayerProgression::class);
    }

    public static function archivedGameExists(string $matchId) {
        return ArchivedGame::where('id', '=', $matchId)->exists();
    }


}
