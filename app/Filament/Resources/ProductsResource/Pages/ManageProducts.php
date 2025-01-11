<?php

namespace App\Filament\Resources\ProductsResource\Pages;

use App\Filament\Resources\ProductsResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;

class ManageProducts extends ManageRecords
{
    protected static string $resource = ProductsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $resource = static::getResource();
                    $data['cloudinary_id'] = $resource::$cloudinaryPublicId;

                    return $data;
                })
                ->modalAlignment(Alignment::Center)
                ->modalWidth(MaxWidth::FitContent)
                ->modalFooterActionsAlignment(Alignment::Right)
                ->createAnother(false),
        ];
    }
}
