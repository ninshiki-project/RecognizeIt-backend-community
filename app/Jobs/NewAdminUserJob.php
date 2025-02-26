<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\User\Invitation\UserCredentialForAdminAccessNotification;
use Filament\Exceptions\NoDefaultPanelSetException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class NewAdminUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param  User  $user
     */
    public function __construct(private readonly User $user) {}

    /**
     * @throws NoDefaultPanelSetException
     */
    public function handle(): void
    {
        // generate a random password
        $tempPassword = Str::random(12);
        $_user = User::find($this->user->id);
        $_user->password = Hash::make($tempPassword);
        $_user->save();

        // send the mailable
        $this->user->notify(new UserCredentialForAdminAccessNotification($tempPassword));

    }
}
