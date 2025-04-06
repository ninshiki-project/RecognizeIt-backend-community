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
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Livewire\Component;

class UpdateStatusAction
{
    /**
     * @param  \Filament\Tables\Actions\Action|Action  $action
     * @return Action|\Filament\Tables\Actions\Action
     */
    public function handle(Action|\Filament\Tables\Actions\Action $action): Action|\Filament\Tables\Actions\Action
    {
        return $action->form([
            Forms\Components\Select::make('status')
                ->label('Change User Account Status')
                ->required()
                ->default(function (User $record) {
                    return $record->status;
                })
                ->native(false)
                ->preload()
                ->disableOptionWhen(function (string $value, User $user): bool {
                    if ($user->status !== UserEnum::Invited) {
                        return $value === UserEnum::Invited->value;
                    }
                    if ($user->status === UserEnum::Invited) {
                        return $value === UserEnum::Active->value;
                    }
                })
                ->options(UserEnum::class),
        ])
            ->modalFooterActionsAlignment(Alignment::Right)
            ->requiresConfirmation()
            ->action(function (User $user, array $data, Component $livewire) {
                $user->update([
                    'status' => $data['status'],
                ]);

                Notification::make('updated')
                    ->success()
                    ->body('User account status updated.')
                    ->send();

                if (method_exists($livewire, 'refreshFormData')) {
                    $livewire->refreshFormData(['status']);
                }

            })
            ->modalWidth(MaxWidth::Small)
            ->modalAlignment(Alignment::Center)
            ->icon('heroicon-o-user-circle')
            ->label('Update Status');
    }
}
