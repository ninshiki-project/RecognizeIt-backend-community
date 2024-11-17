<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Http\Controllers\Api\Enum\UserEnum;
use App\Notifications\User\Invitation\InvitationNotification;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Notification;

class ManageUsers extends ManageRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data) {
                    $data['added_by'] = auth()->id();
                    $data['status'] = UserEnum::Invited->value;

                    return $data;
                })
                ->after(function () {
                    // send invitation email
                    Notification::route('mail', $this->record->email)
                        ->notify(new InvitationNotification);
                })
                ->createAnother(false),
        ];
    }
}
