<?php

namespace App\Models\Game;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperPrestigeRewardItem
 */
class PrestigeRewardItem extends Model
{
    use HasFactory;

    protected $table = 'prestige_rewards';

    public $timestamps = false;

    protected $fillable = [
        'catalog_item_id',
        'amount'
    ];


}
