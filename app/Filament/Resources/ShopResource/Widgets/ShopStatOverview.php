<?php

namespace App\Filament\Resources\ShopResource\Widgets;

use App\Models\Shop;
use Filament\Support\Colors\Color;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ShopStatOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Shop Items', Shop::count())
                ->chart([40, 10, 35, 12, 25, 4, 19])
                ->color(Color::Amber),
        ];
    }
}
