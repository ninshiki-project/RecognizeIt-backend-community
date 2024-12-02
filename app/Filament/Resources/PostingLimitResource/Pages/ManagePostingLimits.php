<?php

namespace App\Filament\Resources\PostingLimitResource\Pages;

use App\Filament\Resources\PostingLimitResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;

class ManagePostingLimits extends ManageRecords
{
    protected static string $resource = PostingLimitResource::class;

    protected ?string $subheading = 'All designation will have a 30 posting limit as default unless defined in the record.';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth(MaxWidth::Medium)
                ->modalAlignment(Alignment::Center)
                ->createAnother(false),
        ];
    }
}
