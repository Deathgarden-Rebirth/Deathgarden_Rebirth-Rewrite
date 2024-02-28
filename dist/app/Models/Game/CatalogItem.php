<?php

namespace App\Models\Game;

use App\Enums\Game\ItemGroupType;
use App\Enums\Game\ItemOrigin;
use App\Enums\Game\ItemQuality;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * @mixin IdeHelperCatalogItem
 */
class CatalogItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'display_name',
        'initial_quantity',
        'consumable',
        'default_cost_currency_a',
        'default_cost_currency_b',
        'default_cost_currency_c',
        'purchasable',
        'meta_min_player_level',
        'meta_min_character_level',
        'meta_is_unbreakable_fullset',
        'meta_origin',
        'meta_quality',
        'meta_group_type',
        'meta_following_item',
        'meta_prerequisite_item',
        'meta_has_bundle_items',
        'has_reward_bundle_items',
    ];

    protected $attributes = [
        'meta_has_bundle_items' => false,
        'has_reward_bundle_items' => false,
    ];

    protected $casts = [
        'meta_origin' => ItemOrigin::class,
        'meta_quality' => ItemQuality::class,
        'meta_group_type' => ItemGroupType::class,
    ];

    public function autoUnlockItems(): BelongsToMany
    {
        return $this->belongsToMany(
            CatalogItem::class,
            'catalog_item_auto_unlock_items',
            'catalog_item_id',
            'unlocked_item_id',
        );
    }

    public function bundleItems(): BelongsToMany {
        return $this->belongsToMany(
            CatalogItem::class,
            'catalog_bundle_items',
            'catalog_item_id',
            'bundle_item',
        );
    }

    public function itemAssignments(): BelongsToMany
    {
        return $this->belongsToMany(
            CatalogItem::class,
            'catalog_item_assignment',
            'catalog_item_id',
            'assigned_item_id',
        );
    }

    public function prestigeRewards(): HasMany
    {
        return $this->hasMany(
            PrestigeReward::class,
        );
    }

    public function requiredChallenges(): BelongsToMany
    {
        return $this->belongsToMany(Challenge::class);
    }

    /**
     * Adds one or multiple gameplay tags to the item.
     *
     * @param string|array $tags
     * @return void
     */
    public function addGameplayTags(string|array $tags): void
    {
        $table = DB::table('catalog_item_meta_gameplay_tags');

        if(!is_array($tags)) {
            $table->insertOrIgnore([
                'catalog_item_id' => $this->id,
                'gameplay_tag' => $tags
            ]);
            return;
        }

        foreach ($tags as $tag) {
            $table->insertOrIgnore([
                'catalog_item_id' => $this->id,
                'gameplay_tag' => $tag
            ]);
        }
    }

    public function getGameplayTags(): array
    {
        $table = DB::table('catalog_item_meta_gameplay_tags');
        $tags = $table->where('catalog_item_id', '=', $this->id)->get(['gameplay_tag']);
        $stringArray = [];

        foreach ($tags as $tag) {
            $stringArray[] = $tag->gameplay_tag;
        }

        return $stringArray;
    }
}
