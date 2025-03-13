<?php

namespace App\Filament\Resources\ProductsResource\Widgets;

use App\Enum\ProductStatusEnum;
use App\Models\Products;
use App\Models\Scopes\ProductAvailableScope;
use Filament\Support\Colors\Color;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProductsStatOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Product', Products::withoutGlobalScopes([new ProductAvailableScope])->count())
                ->description('Register Count')
                ->chart([40, 10, 35, 12, 25, 4, 19])
                ->color(Color::Teal),
            Stat::make('Available Product', Products::count())
                ->description('With stock but limited')
                ->chart([40, 10, 35, 12, 25, 4, 19])
                ->color(Color::Green),
            Stat::make('Unavailable Product', Products::withoutGlobalScopes([new ProductAvailableScope])->whereStatus(ProductStatusEnum::UNAVAILABLE)->count())
                ->description('Out of stock')
                ->chart([40, 10, 35, 12, 25, 4, 19])
                ->color(Color::Red),
        ];
    }
}
