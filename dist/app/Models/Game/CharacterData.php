<?php

namespace App\Models\Game;

use App\Classes\Character\CharacterItemConfig;
use App\Enums\Game\Characters;
use App\Helper\Uuid\UuidHelper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Ramsey\Uuid\Uuid;

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

    public function pickedChallenges(): BelongsToMany
    {
        return $this->belongsToMany(Challenge::class, 'character_data_picked_challenge')
            ->withPivot('catalog_item_id');
    }

    public function getPicketChallengeForItem(Uuid $uuid): Collection
    {
        return $this->pickedChallenges()
            ->where('catalog_item_id', '=', $uuid->toString())
            ->withPivot('catalog_item_id')
            ->get();
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

        if($equippedPerks->count() > $this->character->isHunter() ? CharacterItemConfig::HUNTER_EQUIPPED_PERK_COUNT : CharacterItemConfig::RUNNER_EQUIPPED_PERK_COUNT)
            $this->resetEquippedPerks($itemConfigClass);
        else {
            $allowedPerks = $itemConfigClass::getAllowedPerks();
            $hasUnAllowedPerks = count(array_diff($equippedPerks->toArray(), $allowedPerks)) > 0;

            if($hasUnAllowedPerks)
                $this->resetEquippedPerks($itemConfigClass);
        }

        $equippedWeapons = UuidHelper::convertFromUuidToHexCollection($this->equippedWeapons()->allRelatedIds());

        if($equippedWeapons->count() > $this->character->isHunter() ? CharacterItemConfig::HUNTER_EQUIPPED_WEAPON_COUNT : CharacterItemConfig::RUNNER_EQUIPPED_WEAPON_COUNT)
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
    }

    protected function resetEquippedWeapons(string|CharacterItemConfig $itemConfigClass): void
    {
        // Remove all equipped Weapons and reset to default config
        $this->equippedWeapons()->detach();
        $defaultWeaponIds = UuidHelper::convertFromHexToUuidCollecton($itemConfigClass::getDefaultEquippedWeapons());
        $this->equippedWeapons()->attach($defaultWeaponIds);
    }

    protected function resetEquipment(string|CharacterItemConfig $itemConfigClass): void
    {
        // Remove all Equipment and reset to default config
        $this->equipment()->detach();
        $defaultEquipmentIds = UuidHelper::convertFromHexToUuidCollecton($itemConfigClass::getDefaultEquipment());
        $this->equipment()->attach($defaultEquipmentIds);
    }

    protected function resetEquippedBonuses(string|CharacterItemConfig $itemConfigClass): void
    {
        // Remove all equipped Weapons and reset to default config
        $this->equippedBonuses()->detach();
        $defaultBonusIds = UuidHelper::convertFromHexToUuidCollecton($itemConfigClass::getDefaultEquippedBonuses());
        $this->equippedBonuses()->attach($defaultBonusIds);
    }

}
