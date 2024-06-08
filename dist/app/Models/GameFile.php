<?php

namespace App\Models;

use App\Enums\Launcher\FileAction;
use App\Enums\Launcher\Patchline;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperGameFile
 */
class GameFile extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'patchline' => Patchline::class,
        'action' => FileAction::class,
    ];
}
