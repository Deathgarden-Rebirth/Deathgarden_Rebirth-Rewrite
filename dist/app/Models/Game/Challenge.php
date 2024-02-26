<?php

namespace App\Models\Game;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperChallenge
 */
class Challenge extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'completion_value',
        'asset_path',
    ];
}
