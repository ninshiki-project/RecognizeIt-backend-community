<?php

namespace App\Filament\Widgets;

use App\Http\Controllers\Api\Enum\UserEnum;
use App\Models\Products;
use App\Models\Redeem;
use App\Models\Scopes\ProductAvailableScope;
use App\Models\Shop;
use App\Models\User;
use Filament\Support\Colors\Color;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsOverview extends BaseWidget
{
    protected ?string $heading = 'Analytics';

    protected ?string $description = 'An overview of some analytics.';

    protected function getStats(): array
    {
        return [
            Stat::make('Active Employee Account', User::whereStatus(UserEnum::Active)->count())
                ->chart([40, 10, 35, 12, 25, 4, 19])
                ->color(Color::Purple),
            Stat::make('Total Redeem', Redeem::count())
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color(Color::Fuchsia),
            Stat::make('Total Products', Products::withoutGlobalScopes([new ProductAvailableScope])->count())
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color(Color::Green),
            Stat::make('Shop Item Available', Shop::count())
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color(Color::Amber),
        ];
    }
}
