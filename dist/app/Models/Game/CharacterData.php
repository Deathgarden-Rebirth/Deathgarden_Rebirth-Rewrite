<?php

namespace App\Models\Game;

use App\Classes\Character\CharacterItemConfig;
use App\Enums\Game\Characters;
use App\Helper\Uuid\UuidHelper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    public function equipment(): BelongsToMany
    {
        return $this->belongsToMany(CatalogItem::class,'character_data_equipment');
    }

    public function equippedBonuses(): BelongsToMany
    {
        return $this->belongsToMany(CatalogItem::class,'character_data_equipped_bonuses');
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

    public static function getExperienceForLevel(int $level): int
    {
        --$level;
        // f(x) = 5403 + 5403x * 0.002x
        return 5403 + (5403 * $level) * (0.002 * $level);
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

        $equippedBonuses = $this->equippedBonuses()->allRelatedIds();
        if($equippedBonuses->count() === 0)
            $this->resetEquippedBonuses($itemConfigClass);
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

    protected function resetEquippedBonuses(string|CharacterItemConfig $itemConfigClass): void
    {
        // Remove all equipped Weapons and reset to default config
        $this->equippedBonuses()->detach();
        $defaultBonusIds = UuidHelper::convertFromHexToUuidCollecton($itemConfigClass::getDefaultEquippedBonuses());
        $this->equippedBonuses()->attach($defaultBonusIds);

        $user = Auth::user();
        static::getResetItemsLogger()->warning(sprintf('User %s(%s) had unallowed Bonuses Equipped', $user->id, $user->last_known_username));
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
