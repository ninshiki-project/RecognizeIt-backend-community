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

use App\Enum\UserEnum;
use App\Jobs\NewAdminUserJob;
use App\Jobs\NewUserJob;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Alignment;

class ResendInvitationAction
{
    /**
     * @param  \Filament\Tables\Actions\Action|Action  $action
     * @return Action|\Filament\Tables\Actions\Action
     */
    public function handle(Action|\Filament\Tables\Actions\Action $action): Action|\Filament\Tables\Actions\Action
    {
        return $action->action(function (User $user) {
            if ($user->hasPermissionTo('access panel')) {
                NewAdminUserJob::dispatch($user)
                    ->afterResponse()
                    ->afterCommit();
            }
            NewUserJob::dispatch($user)
                ->afterCommit()
                ->afterResponse();

            Notification::make('resend_invitation')
                ->icon('heroicon-o-paper-airplane')
                ->body('Email Invitation email has been sent.')
                ->success()
                ->send();
        })
            ->visible(fn (User $user): bool => $user->status === UserEnum::Invited)
            ->requiresConfirmation()
            ->modalFooterActionsAlignment(Alignment::Right)
            ->icon('heroicon-o-arrow-path')
            ->label('Resend Invitation Email');
    }
}
