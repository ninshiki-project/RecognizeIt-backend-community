<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make()
                ->action(function ($record, Actions\Action $action) {
                    if ($record->name === config('filament-shield.super_admin.name') || $record->name === config('filament-shield.member.name')) {
                        Notification::make('filament-shield::filament-shield.message.super_admin.deleted')
                            ->body('Unable to delete system defined permissions.')
                            ->warning()
                            ->send();
                        $action->failure();
                        $action->close();

                        return;
                    }
                    $record->delete();
                }),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['guard_name'] = $this->record->guard_name;
        return $data;
    }
}
