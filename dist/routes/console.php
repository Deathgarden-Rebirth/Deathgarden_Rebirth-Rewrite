<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('model:prune')->daily();
Schedule::command('matchmaking:process')->everyTenSeconds();
Schedule::command('matchmaking:cleanup')->everyThirtySeconds();
Schedule::command('app:generate-timed-challenges')->daily();
Schedule::command('app:cleanup-logs')->daily();
// Schedule::command('app:set-currency-modifiers')->hourlyAt(1);
