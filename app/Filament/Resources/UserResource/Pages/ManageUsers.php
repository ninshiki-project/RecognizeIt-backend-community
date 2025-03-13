<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Enum\UserEnum;
use App\Jobs\NewAdminUserJob;
use App\Jobs\NewUserJob;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
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
                ->after(function (User $record) {
                    // send invitation email as system user
                    NewUserJob::dispatch($record)
                        ->afterCommit()
                        ->afterResponse();

                    if ($record->hasPermissionTo('access panel')) {
                        // the queueable will generate a temp password and send email for the temporary credentials
                        NewAdminUserJob::dispatch($record)
                            ->afterCommit()
                            ->afterResponse();
                    }

                })
                ->createAnother(false),
        ];
    }

    public function getHeaderWidgets(): array
    {
        return self::$resource::getWidgets();
    }
}
