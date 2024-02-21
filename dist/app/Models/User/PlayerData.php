<?php

namespace App\Models\User;

use App\Enums\Game\Characters;
use App\Enums\Game\Faction;
use App\Enums\Game\Hunter;
use App\Enums\Game\Runner;
use App\Models\Game\CharacterData;
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

    protected static function booted(): void
    {
        static::created(function ( PlayerData $playerData) {
            foreach (Runner::cases() as $runner) {
                $playerData->characterData()->create(['character' => Characters::from($runner->value)]);
            }

            foreach (Hunter::cases() as $hunter) {
                $playerData->characterData()->create(['character' => Characters::from($hunter->value)]);
            }
        });
    }

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

    public static function getRemainingFactionExperience(int $level): int
    {
         if($level <= 26)
             return 3;
         if($level <= 53)
             return 4;
         return 5;
    }
}
