<?php

namespace App\Models\Game\Matchmaking;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperMatchConfiguration
 */
class MatchConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'enabled',
        'hunters',
        'runners',
        'weight',
        'asset_path',
    ];

    protected $attributes = [
        'enabled' => true,
        'weight' => 100,
        'hunters' => 1,
        'runners' => 5,
    ];
}
