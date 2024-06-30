<?php

namespace App\Console\Commands;

use App\Models\Points;
use Illuminate\Console\Command;

class ResetCreditsCommand extends Command
{
    protected int $credits = 30;

    protected $signature = 'ninshiki:reset-credits';

    protected $description = 'Reset the vote credits back to the default on every end of the month';

    public function handle(): void
    {
        Points::all()->each(function (Points $points) {
            $points->credits = $this->credits;
            $points->save();
        });
    }
}
