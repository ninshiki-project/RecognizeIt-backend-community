<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Redeem Request Today', 0),
            Stat::make('Total Employee', 0),
            Stat::make('Active Employee', 0),
        ];
    }
}
