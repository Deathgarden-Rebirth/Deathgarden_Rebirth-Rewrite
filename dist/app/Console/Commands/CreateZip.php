<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class CreateZip extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zip:create {source} {archivename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create zip archive for folder';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // Start measuring time
        $starttime = microtime(true);

        // Get real path for our folder
        $rootPath = realpath($this->argument('source'));

        // Initialize archive object
        $zip = new \ZipArchive();
        $zip->open(storage_path($this->argument('archivename').'.zip'), \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        // Create recursive directory iterator
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($rootPath),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
        foreach ($files as $name => $file)
        {
            // Skip directories (they would be added automatically)
            if (!$file->isDir())
            {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();

                // Replace backslashes with forwardslashes (if zipped on Windows)
                $filePath = str_replace('\\', '/', $filePath);

                $relativePath = substr($filePath, strlen($rootPath) + 1);

                // Add current file to archive
                $zip->addFile($filePath, $relativePath);
            }
            $this->line($file, 'fg=cyan');
        }
        // Zip archive will be created only after closing object
        $zip->close();
        $this->line("Archive file successfully created", 'fg=green');

        $endtime = microtime(true);
        $this->line("Executing this command took ".$endtime-$starttime." seconds", 'fg=yellow');
    }
}
