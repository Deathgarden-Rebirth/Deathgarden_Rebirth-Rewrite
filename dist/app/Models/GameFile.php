<?php

namespace App\Models;

use App\Enums\Launcher\FileAction;
use App\Enums\Launcher\Patchline;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    public static function getDisk(): FileSystemAdapter {
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

    public function child(): BelongsTo
    {
        return $this->belongsTo(GameFile::class);
    }

    public function getTopParent(): GameFile
    {
        $current = $this;
        while ($current->child) {
            $current = $current->child;
        }
        return $current;
    }

    public function scopeWithFileHistory($query): Collection
    {
        return $query->latest()->get()->pipe(function ($allFiles) {
            $filesById = $allFiles->keyBy('id');
            
            // Find files that are not referenced as child_id (these are the latest in their chains)
            $referencedIds = $allFiles->pluck('child_id')->filter();
            $latestFiles = $allFiles->whereNotIn('id', $referencedIds);
            
            // Build complete history chains for each latest file
            return $latestFiles->map(function ($latestFile) use ($filesById) {
                $history = collect();
                
                // Walk backwards through the chain to collect all history
                $currentId = $latestFile->child_id;
                while ($currentId && isset($filesById[$currentId])) {
                    $historyFile = $filesById[$currentId];
                    $history->push($historyFile);
                    $currentId = $historyFile->child_id;
                }
                
                $latestFile->children = $history;
                return $latestFile;
            })->sortByDesc('updated_at');
        });
    }
}
