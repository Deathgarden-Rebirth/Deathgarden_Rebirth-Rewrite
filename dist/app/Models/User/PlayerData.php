<?php

namespace App\Models\User;

use App\Classes\Character\CharacterItemConfig;
use App\Enums\Game\Characters;
use App\Enums\Game\Faction;
use App\Enums\Game\Hunter;
use App\Enums\Game\Runner;
use App\Helper\Uuid\UuidHelper;
use App\Models\Game\CatalogItem;
use App\Models\Game\Challenge;
use App\Models\Game\CharacterData;
use App\Models\Game\QuitterState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Artisan;


/**
 * @mixin IdeHelperPlayerData
 */
class PlayerData extends Model
{
    use HasFactory;

    protected $attributes = [
        'has_played_tutorial' => false,
        'has_played_dg_1' => false,
        'currency_a' => 1000,
        'currency_b' => 500,
        'currency_c' => 500,
        'last_faction' => Faction::Runner,
        'last_hunter' => Hunter::Inquisitor,
        'last_runner' => Runner::Smoke,
        'readout_version' => 1,
        'runner_faction_level' => 1,
        'hunter_faction_level' => 1,
        'runner_faction_experience' => 0,
        'hunter_faction_experience' => 0
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
                // Add Default items into inventory
                /** @var CharacterItemConfig $configClass */
                $configClass = $runner->getItemConfigClass();
                $itemIds = [
                    ...$configClass::getDefaultEquippedBonuses(),
                    ...$configClass::getDefaultEquippedWeapons(),
                    ...$configClass::getDefaultEquipment(),
                    ...$configClass::getDefaultPowers(),
                    ...$configClass::getDefaultEquippedPerks(),
                ];

                try {
                    $playerData->inventory()->attach(UuidHelper::convertFromHexToUuidCollecton($itemIds)->toArray());
                }
                catch (QueryException $e) {}
            }

            foreach (Hunter::cases() as $hunter) {
                $playerData->characterData()->create(['character' => Characters::from($hunter->value)]);
                // Add Default items into inventory
                /** @var CharacterItemConfig $configClass */
                $configClass = $hunter->getItemConfigClass();
                $itemIds = [
                    ...$configClass::getDefaultEquippedBonuses(),
                    ...$configClass::getDefaultEquippedWeapons(),
                    ...$configClass::getDefaultEquipment(),
                    ...$configClass::getDefaultPowers(),
                    ...$configClass::getDefaultEquippedPerks(),
                ];

                try {
                    $playerData->inventory()->attach(UuidHelper::convertFromHexToUuidCollecton($itemIds));
                }
                catch (QueryException $e) {}
            }

            $playerData->quitterState()->create();

            // Add Default Characters
            Artisan::call('game:add-characters-to-user', [
                'user' => $playerData->user_id,
                'characters' => [
                    Characters::Smoke->value,
                    Characters::Ghost->value,
                    Characters::Sawbones->value,
                    Characters::Inquisitor->value,
                ]
            ]);
        });
    }

    public function characterData(): HasMany
    {
        return $this->hasMany(CharacterData::class);
    }

    public function inventory(): BelongsToMany
    {
        return $this->belongsToMany(CatalogItem::class)->withTimestamps();
    }

    public function characterDataForCharacter(Characters $character): CharacterData|Model|null {
        return $this->characterData()->firstWhere('character', $character->value);
    }

    public function lastHunterCharacterData(): CharacterData|Model  {
        $attributeValue = ['character' => $this->last_hunter];
        return $this->hasMany(CharacterData::class)->firstOrCreate($attributeValue, $attributeValue);
    }

    public function lastRunnerCharacterData(): CharacterData|Model  {
        $attributeValue = ['character' => $this->last_runner];
        return $this->hasMany(CharacterData::class)->firstOrCreate($attributeValue, $attributeValue);
    }

    public function challenges(): BelongsToMany
    {
        return $this->belongsToMany(Challenge::class)->withPivot(['progress']);
    }

    public function quitterState(): HasOne
    {
        return $this->hasOne(QuitterState::class);
    }

    public function getCumulativeExperience(): int
    {
        $characterData = $this->characterData;
        $experience = 0;

        foreach ($characterData as $character) {
            $experience += $character->experience;
        }

        return $experience;
    }

    public function addFactionExperience(Faction $faction): PlayerData
    {
        if($faction === Faction::Runner) {
            ++$this->runner_faction_experience;

            if($this->runner_faction_experience >= static::getRemainingFactionExperience($this->runner_faction_level)) {
                ++$this->runner_faction_level;
                $this->runner_faction_experience = 0;
            }
        }
        else if($faction === Faction::Hunter) {
            ++$this->hunter_faction_experience;

            if($this->hunter_faction_experience >= static::getRemainingFactionExperience($this->hunter_faction_level)) {
                ++$this->hunter_faction_level;
                $this->hunter_faction_experience = 0;
            }
        }

        return $this;
    }

    public static function getRemainingFactionExperience(int $level): int
    {
         if($level < 10)
             return 2;
         if($level < 20)
             return 3;
        if($level < 50)
            return 4;
         return 5;
    }
}
