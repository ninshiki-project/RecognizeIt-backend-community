<?php

namespace App\Filament\Resources\RedeemResource\Pages;

use App\Filament\Resources\RedeemResource;
use App\Http\Controllers\Api\Enum\RedeemStatusEnum;
use Filament\Resources\Components;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;

class ManageRedeems extends ManageRecords
{
    protected static string $resource = RedeemResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'For Approval';
    }

    public function getTabs(): array
    {

        return [
            'all' => Components\Tab::make()
                ->label('All Redeems')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', '!=', RedeemStatusEnum::CANCELED)),
            'For Approval' => Components\Tab::make()
                ->label('Awaiting Approval')
                ->icon(RedeemStatusEnum::WAITING_APPROVAL->getIcon())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', '=', RedeemStatusEnum::WAITING_APPROVAL)),
            'Approved' => Components\Tab::make()
                ->label('Approved')
                ->icon(RedeemStatusEnum::APPROVED->getIcon())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', '=', RedeemStatusEnum::APPROVED)),
            'Processing' => Components\Tab::make()
                ->label('Processing')
                ->icon(RedeemStatusEnum::PROCESSING->getIcon())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', '=', RedeemStatusEnum::PROCESSING)),
            'Redeemed' => Components\Tab::make()
                ->label('Redeemed')
                ->icon(RedeemStatusEnum::REDEEMED->getIcon())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', '=', RedeemStatusEnum::REDEEMED)),
            'Declined' => Components\Tab::make()
                ->label('Declined')
                ->icon(RedeemStatusEnum::DECLINED->getIcon())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', '=', RedeemStatusEnum::DECLINED)),
        ];
    }
}
