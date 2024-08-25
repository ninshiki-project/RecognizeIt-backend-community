<?php

namespace App\Console\Commands;

use App\Models\User;
use Bavix\Wallet\Internal\Exceptions\ExceptionInterface;
use Illuminate\Console\Command;

class ResetSpendWalletCommand extends Command
{
    protected $signature = 'ninshiki:reset-wallet';

    protected $description = 'Reset the spend coin back to the default on every end of the month';

    /**
     * @throws ExceptionInterface
     */
    public function handle(): void
    {
        User::all()->each(function (User $user) {
            $wallet = $user->getWallet('spend-wallet');
            $remaining = $wallet->balance ?? 0;
            $reminder = (config('ninshiki.fund.normal') - $remaining);
            if ($reminder < config('ninshiki.fund.normal')) {
                $wallet->deposit($reminder, [
                    'title' => 'Spend Wallet',
                    'description' => 'Reset the spend coin back to the default on every end of the month',
                ]);
            }
        });
    }
}
