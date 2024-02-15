<?php

namespace App\Models;

use App\Enums\Game\Faction;
use App\Enums\Game\Hunter;
use App\Enums\Game\Runner;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperPlayerData
 */
class PlayerData extends Model
{
    use HasFactory;

    protected $attributes = [
        'has_played_tutorial' => false,
        'has_played_dg_1' => true,
        'last_faction' => Faction::Runner,
        'last_hunter' => Hunter::Poacher,
        'last_runner' => Runner::Smoke,
    ];

    protected $casts = [
        'last_faction' => Faction::class,
        'last_hunter' => Hunter::class,
        'last_runner' => Runner::class,
        'has_played_tutorial' => 'boolean',
        'has_played_dg_1' => 'boolean',
    ];

    public function characterData(): HasMany
    {
        return $this->hasMany(CharacterData::class);
    }

    public function lastHunterCharacterData(): CharacterData|Model  {
        $attributeValue = ['character' => $this->last_hunter];
        return $this->hasMany(CharacterData::class)->firstOrCreate($attributeValue, $attributeValue);
    }

    public function lastRunnerCharacterData(): CharacterData|Model  {
        $attributeValue = ['character' => $this->last_runner];
        return $this->hasMany(CharacterData::class)->firstOrCreate($attributeValue, $attributeValue);
    }
}
