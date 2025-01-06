<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use DirectoryIterator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Str;

class CleanupLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup old log files.';

    // Delete files older than this number of days.
    const FILE_DAYS_OLD = 14;

    // Delete session files older than this number of days.
    const SESSION_FILE_DAYS_OLD = 7;


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $disk = Storage::disk('logs');
        $files = $disk->allFiles();
        $now = Carbon::now();

        // Delete older modified files
        foreach ($files as $file) {
            $lastModified = Carbon::createFromTimestamp($disk->lastModified($file));

            if(Str::startsWith($file, 'sessions/'))
                $shouldDelete = $now->diff($lastModified)->total('days') * -1 > self::SESSION_FILE_DAYS_OLD;
            else
                $shouldDelete = $now->diff($lastModified)->total('days') * -1 > self::FILE_DAYS_OLD;

            if($shouldDelete)
                $disk->delete($file);
        }

        //Loop over all directories to delete empty ones.
        $directories = $disk->allDirectories();
        foreach ($directories as $directory) {
            $files = $disk->allFiles($directory);

            if(count($files) === 0)
                $disk->deleteDirectory($directory);
        }
    }
}
