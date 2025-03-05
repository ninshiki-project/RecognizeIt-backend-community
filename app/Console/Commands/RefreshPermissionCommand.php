<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RefreshPermissionCommand extends Command
{
    protected $signature = 'refresh:permission';

    protected $description = 'Resynchronize permissions';

    public function handle(): void
    {
        $this->call('shield:generate', ['--all' => true, '--panel' => 'admin']);
        $this->components->success('Permissions refreshed');
        $this->components->info('Administrator Permissions has been updated.');
    }
}
