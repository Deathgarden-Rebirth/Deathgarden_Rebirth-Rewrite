<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('model:prune')->daily();
        $schedule->command('matchmaking:process')->everyTenSeconds();
        $schedule->command('matchmaking:cleanup')->everyThirtySeconds();
        $schedule->command('app:generate-timed-challenges')->daily();
        $schedule->command('app:cleanup-logs')->daily();
        $schedule->command('app:set-currency-modifiers')->hourlyAt(1);
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
