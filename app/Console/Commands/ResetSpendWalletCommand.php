<?php

namespace App\Console\Commands;

use App\Models\User;
use Bavix\Wallet\Models\Wallet;
use Illuminate\Console\Command;

class ResetSpendWalletCommand extends Command
{
    protected $signature = 'ninshiki:reset-wallet';

    protected $description = 'Reset the spend coin back to the default on every end of the month';

    public function handle(): void
    {
        User::all()->each(function (User $user) {
            $wallet = Wallet::where('holder_id', $user->id)
                ->where('slug', 'spend-wallet')->first();
            $wallet->balance = $user->designations?->postingLimits?->limit ?? 30;
            $wallet->save();
        });

    }
}
