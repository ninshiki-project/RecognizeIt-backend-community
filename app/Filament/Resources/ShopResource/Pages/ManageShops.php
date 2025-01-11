<?php

namespace App\Filament\Resources\ShopResource\Pages;

use App\Filament\Resources\ShopResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;

class ManageShops extends ManageRecords
{
    protected static string $resource = ShopResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalFooterActionsAlignment(Alignment::Right)
                ->modalWidth(MaxWidth::Small)
                ->modalAlignment(Alignment::Center)
                ->createAnother(false),
        ];
    }
}
