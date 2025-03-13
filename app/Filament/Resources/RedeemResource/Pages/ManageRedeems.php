<?php

namespace App\Filament\Resources\RedeemResource\Pages;

use App\Filament\Resources\RedeemResource;
use App\Enum\RedeemStatusEnum;
use App\Models\Redeem;
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
        return 'all';
    }

    public function getTabs(): array
    {

        return [
            'all' => Components\Tab::make()
                ->label('All Redeems')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', '!=', RedeemStatusEnum::CANCELED)->orderByDesc('created_at')),
            RedeemStatusEnum::WAITING_APPROVAL->value => Components\Tab::make()
                ->label('Awaiting Approval')
                ->icon(RedeemStatusEnum::WAITING_APPROVAL->getIcon())
                ->badge(function (): ?int {
                    $count = Redeem::query()->where('status', '=', RedeemStatusEnum::WAITING_APPROVAL)->count();

                    return $count > 0 ? $count : null;
                })
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', '=', RedeemStatusEnum::WAITING_APPROVAL)),
            RedeemStatusEnum::APPROVED->value => Components\Tab::make()
                ->label('Approved')
                ->icon(RedeemStatusEnum::APPROVED->getIcon())
                ->badge(function (): ?int {
                    $count = Redeem::query()->where('status', '=', RedeemStatusEnum::APPROVED)->count();

                    return $count > 0 ? $count : null;
                })
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', '=', RedeemStatusEnum::APPROVED)),
            RedeemStatusEnum::PROCESSING->value => Components\Tab::make()
                ->label('Processing')
                ->icon(RedeemStatusEnum::PROCESSING->getIcon())
                ->badge(function (): ?int {
                    $count = Redeem::query()->where('status', '=', RedeemStatusEnum::PROCESSING)->count();

                    return $count > 0 ? $count : null;
                })
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', '=', RedeemStatusEnum::PROCESSING)),
            RedeemStatusEnum::REDEEMED->value => Components\Tab::make()
                ->label('Redeemed')
                ->icon(RedeemStatusEnum::REDEEMED->getIcon())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', '=', RedeemStatusEnum::REDEEMED)),
            RedeemStatusEnum::CANCELED->value => Components\Tab::make()
                ->label('Declined')
                ->icon(RedeemStatusEnum::DECLINED->getIcon())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', '=', RedeemStatusEnum::DECLINED)),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return self::$resource::getWidgets();
    }
}
