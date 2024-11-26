<?php

namespace App\Filament\Resources\DesignationsResource\Pages;

use App\Filament\Resources\DesignationsResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;

class ManageDesignations extends ManageRecords
{
    protected static string $resource = DesignationsResource::class;

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
