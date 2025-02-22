<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\User\Invitation\InvitationNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NewUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param  User  $user
     */
    public function __construct(private readonly User $user) {}

    public function handle(): void
    {
        $this->user->notify(new InvitationNotification);
    }
}
