<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Actions\UserResource\PasswordResetRequestAction;
use App\Filament\Actions\UserResource\ResendInvitationAction;
use App\Filament\Actions\UserResource\SendGiftAction;
use App\Filament\Actions\UserResource\UpdateRoleAction;
use App\Filament\Actions\UserResource\UpdateStatusAction;
use App\Filament\Resources\UserResource;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Str;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    public function getTitle(): string
    {
        $name = Str::endsWith($this->record->name, 's') ? $this->record->name."'" : $this->record->name."'s";

        return "{$name} Record";
    }

    protected function getHeaderActions(): array
    {
        return [
            ...(new SendGiftAction)->handle(Action::make('gift')),
            EditAction::make('update')
                ->icon('heroicon-o-pencil')
                ->modalWidth(MaxWidth::Small)
                ->modalAlignment(Alignment::Center)
                ->modalFooterActionsAlignment(Alignment::Right)
                ->after(fn () => $this->refreshFormData(['department', 'designation'])),
            ActionGroup::make([
                (new UpdateStatusAction)->handle(Action::make('update_status')),
                (new ResendInvitationAction)->handle(Action::make('resend_invitation')),
                (new UpdateRoleAction)->handle(Action::make('update_role')),
                ActionGroup::make([
                    (new PasswordResetRequestAction)->handle(Action::make('password_reset')),
                ])->dropdown(false),

            ])->label('More actions')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size(ActionSize::Small)
                ->color('primary')
                ->button(),
        ];
    }
}
