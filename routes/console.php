<?php

use App\Console\Commands\ResetSpendWalletCommand;
use Illuminate\Support\Facades\Schedule;

/**
 * Scheduled Command
 */
Schedule::command(ResetSpendWalletCommand::class)
    ->lastDayOfMonth('23:59')
    ->runInBackground();
Schedule::command('auth:clear-resets')
    ->everyFifteenMinutes();
Schedule::call(fn () => \App\Events\Broadcast\SessionHealthCheckEvent::dispatch())
    ->everyThreeMinutes();
Schedule::command('pulse:clear --force')
    ->at('23:59')
    ->sundays();
Schedule::command('authentication-log:purge')->monthly();
