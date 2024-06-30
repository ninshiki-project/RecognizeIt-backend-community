<?php

use App\Console\Commands\ResetCreditsCommand;
use Illuminate\Support\Facades\Schedule;


/**
 * Scheduled Command
 */
Schedule::command(ResetCreditsCommand::class)->lastDayOfMonth('23:50');
