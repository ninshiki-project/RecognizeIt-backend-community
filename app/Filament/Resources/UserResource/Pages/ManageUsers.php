<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Http\Controllers\Api\Enum\UserEnum;
use App\Notifications\User\Invitation\InvitationNotification;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class ManageUsers extends ManageRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth(MaxWidth::TwoExtraLarge)
                ->modalAlignment(Alignment::Center)
                ->mutateFormDataUsing(function (array $data) {
                    $data['added_by'] = auth()->id();
                    $data['status'] = UserEnum::Invited;
                    $data['username'] = Str::slug($data['name'], '_');

                    return $data;
                })
                ->after(function () {
                    // send invitation email
                    /** @phpstan-ignore-next-line  */
                    Notification::route('mail', $this->record->email)
                        ->notify(new InvitationNotification);
                })
                ->createAnother(false),
        ];
    }

    public function getHeaderWidgets(): array
    {
        return self::$resource::getWidgets();
    }
}
