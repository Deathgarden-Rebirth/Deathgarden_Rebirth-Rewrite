<?php

namespace App\Models\Game;

use App\Enums\Game\Characters;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperCharacterData
 */
class CharacterData extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'character',
    ];

    protected $casts = [
        'character' => Characters::class,
    ];

    protected $attributes = [
        'readout_version' => 1,
    ];

    public static function getExperienceForLevel(int $level): int
    {
        --$level;
        // f(x) = 5403 + 5403x * 0.002x
        return 5403 + (5403 * $level) * (0.002 * $level);
    }

    // This class is later extendable if we know more how to send the equipped items, weapons, perks ect. with the InitOrGetGroups endpoint
}
