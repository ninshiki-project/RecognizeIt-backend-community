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
                    if ($data['image_using'] === 'link') {
                        $data['image'] = $data['image_link'];
                    }
                    unset($data['image_using']);
                    unset($data['image_link']);
                    $data['cloudinary_id'] = $resource::$cloudinaryPublicId;

                    return $data;
                })
                ->modalAlignment(Alignment::Center)
                ->modalWidth(MaxWidth::Medium)
                ->modalFooterActionsAlignment(Alignment::Right)
                ->createAnother(false),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return self::$resource::getWidgets();
    }
}
