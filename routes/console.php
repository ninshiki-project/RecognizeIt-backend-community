<?php

use App\Console\Commands\ResetSpendWalletCommand;
use Illuminate\Support\Facades\Schedule;

/**
 * Scheduled Command
 */
Schedule::command(ResetSpendWalletCommand::class)
    ->lastDayOfMonth('23:50')
    ->runInBackground();
Schedule::command('auth:clear-resets')
    ->everyFifteenMinutes()
    ->runInBackground();
Schedule::call(fn () => \App\Events\Broadcast\SessionHealthCheckEvent::dispatch())
    ->everyThreeMinutes();
