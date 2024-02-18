<?php

namespace App\Models\Game;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperPrestigeReward
 */
class PrestigeReward extends Model
{
    use HasFactory;

    protected $table = 'catalog_prestige_rewards';

    public $timestamps = false;

    protected $fillable = [
        'catalog_item_id',
        'cost_currency_a',
        'cost_currency_b',
        'cost_currency_c',
    ];

    public function rewardItems(): HasMany
    {
        return $this->hasMany(
            PrestigeRewardItem::class,
            'prestige_reward_id',
            'catalog_item_id',
        );
    }
}
