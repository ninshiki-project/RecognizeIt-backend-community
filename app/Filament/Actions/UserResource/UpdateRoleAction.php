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

use App\Models\Role;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Livewire\Component;

class UpdateRoleAction
{
    /**
     * @param  \Filament\Tables\Actions\Action|Action  $action
     * @return Action|\Filament\Tables\Actions\Action
     */
    public function handle(Action|\Filament\Tables\Actions\Action $action): Action|\Filament\Tables\Actions\Action
    {
        return $action->form([
            Forms\Components\Select::make('roles')
                ->label('Change User Role to:')
                ->required()
                ->live()
                ->reactive()
                ->native(false)
                ->preload()
                ->default(function (User $record) {
                    return $record->getRoleNames()[0] ?? null;
                })
                ->options(Role::all()->pluck('name', 'name')),
            Forms\Components\TextInput::make('password')
                ->label('Set Temporary Password:')
                ->reactive()
                ->hidden(function (Forms\Get $get) {
                    if (is_null($get('roles'))) {
                        return true;
                    }
                    $role = Role::where('name', $get('roles'))->first();
                    if ($role->hasPermissionTo('access panel')) {
                        return false;
                    } else {
                        return true;
                    }
                })
                ->revealable()
                ->required(function (Forms\Get $get) {
                    $role = Role::where('name', $get('roles'))->first();
                    if ($role->hasPermissionTo('access panel')) {
                        return true;
                    } else {
                        return false;
                    }
                })
                ->password(),
        ])
            ->modalFooterActionsAlignment(Alignment::Right)
            ->requiresConfirmation()
            ->action(function (User $user, array $data, Component $livewire) {
                $user->update([
                    'password' => $data['password'] ?? null,
                ]);

                $user->syncRoles($data['roles']);

                Notification::make('updated')
                    ->success()
                    ->body('User account role updated.')
                    ->send();

                if (method_exists($livewire, 'refreshFormData')) {
                    $livewire->refreshFormData(['status']);
                }

            })
            ->modalWidth(MaxWidth::Small)
            ->modalAlignment(Alignment::Center)
            ->icon('heroicon-o-shield-check')
            ->label('Update Role');
    }
}
