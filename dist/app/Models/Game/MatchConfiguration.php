<?php

namespace App\Models\Game;

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
        'weight',
        'asset_path',
    ];

    protected $attributes = [
        'enabled' => true,
        'weight' => 100,
    ];
}
