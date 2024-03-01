<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateMigrationKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migration_key:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a temporary migration key';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $key = Str::random(32); // Generate a random key
        Storage::put('migration_key.txt', $key); // Store the key in a file named migration_key.txt in the root directory
        
        $this->info($key);
    }
}
