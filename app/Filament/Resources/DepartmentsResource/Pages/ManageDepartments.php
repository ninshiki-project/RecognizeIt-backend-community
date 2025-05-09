<?php

namespace App\Filament\Resources\DepartmentsResource\Pages;

use App\Filament\Resources\DepartmentsResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;

class ManageDepartments extends ManageRecords
{
    protected static string $resource = DepartmentsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth(MaxWidth::Small)
                ->modalAlignment(Alignment::Center)
                ->createAnother(false),
        ];
    }
}
