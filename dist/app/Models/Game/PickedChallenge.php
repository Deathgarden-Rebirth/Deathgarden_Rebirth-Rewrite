<?php

namespace App\Models\Game;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperPickedChallenge
 */
class PickedChallenge extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'completion_value',
        'asset_path',
    ];

    protected $attributes = [
        'progress' => 0,
    ];

    public function characterData(): BelongsTo
    {
        return $this->belongsTo(CharacterData::class);
    }

    public function catalogItem(): BelongsTo
    {
        return $this->belongsTo(CatalogItem::class);
    }
}
