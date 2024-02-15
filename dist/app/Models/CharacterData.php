<?php

namespace App\Models;

use App\Enums\Game\Characters;
use App\Enums\Game\Hunter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperCharacterData
 */
class CharacterData extends Model
{
    use HasFactory;

    protected $fillable = [
        'character'
    ];

    protected $casts = [
        'character' => Characters::class,
    ];

    // This class is later extendable if we know more how to send the equipped items, weapons, perks ect. with the InitOrGetGroups endpoint
}
