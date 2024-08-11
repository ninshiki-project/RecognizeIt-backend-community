<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Random\RandomException;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'reverb:key')]
class GenerateReverbKeyCommand extends Command
{
    protected $signature = 'reverb:key';

    protected $description = 'Generate Reverb Key, ID, and Secret Key';

    /**
     * @throws RandomException
     */
    public function handle(): void
    {
        $appId = random_int(100_000, 999_999);
        $appKey = Str::lower(Str::random(20));
        $appSecret = Str::lower(Str::random(20));
        $this->info('Copy and paste the following key into the your env file:');
        $this->newLine(1);
        $this->info('REVERB_APP_ID = \''.$appId.'\';');
        $this->info('REVERB_APP_KEY = \''.$appKey.'\';');
        $this->info('REVERB_APP_SECRET = \''.$appSecret.'\';');
    }
}
