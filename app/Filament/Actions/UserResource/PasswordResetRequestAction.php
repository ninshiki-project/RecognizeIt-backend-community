<?php

/*
 * Copyright (c) 2025.
 *
 * Filename: UpdateStatusAction.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Filament\Actions\UserResource;

use App\Jobs\AdminPasswordResetRequestJob;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Alignment;

class PasswordResetRequestAction
{
    /**
     * @param  \Filament\Tables\Actions\Action|Action  $action
     * @return Action|\Filament\Tables\Actions\Action
     */
    public function handle(Action|\Filament\Tables\Actions\Action $action): Action|\Filament\Tables\Actions\Action
    {
        return $action->action(function (User $user) {
            AdminPasswordResetRequestJob::dispatch($user)
                ->afterResponse()
                ->afterCommit();
            Notification::make('password_reset')
                ->icon('heroicon-o-paper-airplane')
                ->body('Password reset email has been sent.')
                ->success()
                ->send();
        })
            ->visible(fn (User $user): bool => $user->hasPermissionTo('access panel'))
            ->requiresConfirmation()
            ->modalFooterActionsAlignment(Alignment::Right)
            ->icon('heroicon-o-finger-print')
            ->label('Request Password Reset');
    }
}
