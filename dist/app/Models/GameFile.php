<?php

namespace App\Models;

use App\Enums\Launcher\FileAction;
use App\Enums\Launcher\Patchline;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

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

    protected static ?FilesystemAdapter $disk = null;

    protected static function booted()
    {
        static::deleting(function (GameFile $gameFile) {
            static::getDisk()->delete($gameFile->getDiskPath());
        });
    }

    public static function getDisk():FileSystemAdapter {
        return static::$disk ?? static::$disk = Storage::disk('patches');
    }

    public function getDiskPath(): string {
        return strtolower($this->patchline->name).'/'.$this->filename;
    }

    public function fileExists(): bool
    {
        return static::getDisk()->exists($this->getDiskPath());
    }

    /**
     * @return int File size in bytes
     */
    public function getFileSize(): int {
        return static::getDisk()->size($this->getDiskPath());
    }
}
