<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $runners = [2,2,2,1];
        $hunters = 2;
        $allowedGroupSizes = [0,4,5,6];

        $runnerWaitTimes = [1,2,1,3];
        $hunterWaittimes = [1,2];

        $timeMultiplier = [
            1 => 1,
            2 => 0.9,
            3 => 0.8,
        ];

        $path = resource_path('scripts/matchmaking');

        $result = exec($path);
        $this->info($result);
    }
}
