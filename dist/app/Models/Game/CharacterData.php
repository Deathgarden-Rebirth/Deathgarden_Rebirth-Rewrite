<?php

namespace App\Models\Game;

use App\Classes\Character\CharacterItemConfig;
use App\Enums\Game\Characters;
use App\Helper\Uuid\UuidHelper;
use App\Models\User\PlayerData;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\UuidInterface;

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

    static ?LoggerInterface $resetItemsLogger = null;

    public function playerData(): BelongsTo
    {
        return $this->belongsTo(PlayerData::class);
    }

    public function equipment(): BelongsToMany
    {
        return $this->belongsToMany(CatalogItem::class,'character_data_equipment');
    }

    public function equippedPerks(): BelongsToMany
    {
        return $this->belongsToMany(CatalogItem::class,'character_data_equipped_perks');
    }

    public function equippedWeapons(): BelongsToMany
    {
        return $this->belongsToMany(CatalogItem::class,'character_data_equipped_weapons');
    }

    public function pickedChallenges(): HasMany
    {
        return $this->hasMany(PickedChallenge::class);
    }

    public function getPicketChallengeForItem(UuidInterface $uuid): Collection|PickedChallenge|null
    {
        $challenges = $this->pickedChallenges;

        return $challenges->firstWhere('catalog_item_id', '=', $uuid->toString());
    }

    public function addExperience(int $experienceToAdd): CharacterData
    {
        $xpToReach = static::getExperienceForLevel($this->level);
        $this->experience += $experienceToAdd;

        // if we reached teh experience threshold, add a level to the character and faction
        // Set the new xp to reach and do it again if necessary.
        while ($this->experience >= $xpToReach) {
            ++$this->level;
            $this->experience -= $xpToReach;
            $this->playerData->addFactionExperience($this->character->getFaction());
            $xpToReach = static::getExperienceForLevel($this->level);
            $this->playerData->save();
        }

        return $this;
    }

    public static function getExperienceForLevel(int $level): int
    {
        --$level;
        // f(x) = 12500 + 20000x * 0.0004x
        return 12500 + (20000 * $level) * (0.0004 * $level);
    }

    public function validateEquippedItems(): void
    {
        /** @var CharacterItemConfig|string $itemConfigClass */
        $itemConfigClass = $this->character->getCharacter()->getItemConfigClass();

        $equippedPerks = UuidHelper::convertFromUuidToHexCollection($this->equippedPerks()->allRelatedIds());

        if($equippedPerks->count() > ($this->character->isHunter() ? CharacterItemConfig::HUNTER_EQUIPPED_PERK_COUNT : CharacterItemConfig::RUNNER_EQUIPPED_PERK_COUNT))
            $this->resetEquippedPerks($itemConfigClass);
        else {
            $allowedPerks = $itemConfigClass::getAllowedPerks();
            $hasUnAllowedPerks = count(array_diff($equippedPerks->toArray(), $allowedPerks)) > 0;

            if($hasUnAllowedPerks)
                $this->resetEquippedPerks($itemConfigClass);
        }

        $equippedWeapons = UuidHelper::convertFromUuidToHexCollection($this->equippedWeapons()->allRelatedIds());

        if($equippedWeapons->count() > ($this->character->isHunter() ? CharacterItemConfig::HUNTER_EQUIPPED_WEAPON_COUNT : CharacterItemConfig::RUNNER_EQUIPPED_WEAPON_COUNT))
            $this->resetEquippedWeapons($itemConfigClass);
        else {
            $allowedWeapons = $itemConfigClass::getAllowedWeapons();
            $hasUnAllowedWeapons = count(array_diff($equippedWeapons->toArray(), $allowedWeapons)) > 0;

            if($hasUnAllowedWeapons)
                $this->resetEquippedWeapons($itemConfigClass);
        }

        $equippedEquipment = $this->equipment()->allRelatedIds();
        if($equippedEquipment->count() === 0)
            $this->resetEquipment($itemConfigClass);
    }

    protected function resetEquippedPerks(string|CharacterItemConfig $itemConfigClass): void
    {
        // Remove all equipped Perks and reset to default config
        $this->equippedPerks()->detach();
        $defaultPerkIds = UuidHelper::convertFromHexToUuidCollecton($itemConfigClass::getDefaultEquippedPerks());
        $this->equippedPerks()->attach($defaultPerkIds);

        $user = Auth::user();
        static::getResetItemsLogger()->warning(sprintf('User %s(%s) had unallowed Perks Equipped', $user->id, $user->last_known_username));
    }

    protected function resetEquippedWeapons(string|CharacterItemConfig $itemConfigClass): void
    {
        // Remove all equipped Weapons and reset to default config
        $this->equippedWeapons()->detach();
        $defaultWeaponIds = UuidHelper::convertFromHexToUuidCollecton($itemConfigClass::getDefaultEquippedWeapons());
        $this->equippedWeapons()->attach($defaultWeaponIds);

        $user = Auth::user();
        static::getResetItemsLogger()->warning(sprintf('User %s(%s) had unallowed Weapons Equipped', $user->id, $user->last_known_username));
    }

    protected function resetEquipment(string|CharacterItemConfig $itemConfigClass): void
    {
        // Remove all Equipment and reset to default config
        $this->equipment()->detach();
        $defaultEquipmentIds = UuidHelper::convertFromHexToUuidCollecton($itemConfigClass::getDefaultEquipment());
        $this->equipment()->attach($defaultEquipmentIds);

        $user = Auth::user();
        static::getResetItemsLogger()->warning(sprintf('User %s(%s) had unallowed Equipment Equipped', $user->id, $user->last_known_username));
    }

    protected static function getResetItemsLogger(): LoggerInterface
    {
        if(static::$resetItemsLogger === null) {
            static::$resetItemsLogger = Log::build([
                'driver' => 'single',
                'path' => storage_path('logs/resetEquippedItems.log'),
            ]);
        }

        return static::$resetItemsLogger;
    }

}
