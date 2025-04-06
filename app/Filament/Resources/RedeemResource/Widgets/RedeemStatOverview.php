<?php

namespace App\Filament\Resources\RedeemResource\Widgets;

use App\Enum\RedeemStatusEnum;
use App\Models\Redeem;
use Filament\Support\Colors\Color;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RedeemStatOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(\Str::plural('Total Record', Redeem::count()), Redeem::count())
                ->description('Total Redeemed Items')
                ->chart([40, 10, 35, 12, 25, 4, 19])
                ->color(Color::Sky),
            Stat::make('Awaiting Approval Redeem', Redeem::where('status', RedeemStatusEnum::WAITING_APPROVAL)->whereDate('created_at', today())->count())
                ->description('Total Awaiting Approval Redeemed Items')
                ->chart([40, 10, 35, 12, 25, 4, 19])
                ->color(Color::Purple),
            Stat::make('Approved Redeem', Redeem::where('status', RedeemStatusEnum::APPROVED)->count())
                ->description('Total Approved Redeem')
                ->chart([40, 10, 35, 12, 25, 4, 19])
                ->color(Color::Green),
            Stat::make('Declined Redeem', Redeem::where('status', RedeemStatusEnum::DECLINED)->count())
                ->description('Total Declined Redeem')
                ->chart([40, 10, 35, 12, 25, 4, 19])
                ->color(Color::Red),
        ];
    }
}
