<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Http\Controllers\Api\Enum\UserEnum;
use App\Models\User;
use Filament\Support\Colors\Color;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Account', User::count())
                ->chart([40, 10, 35, 12, 25, 4, 19])
                ->color(Color::Amber),
            Stat::make('Active Employee Account', User::whereStatus(UserEnum::Active)->count())
                ->chart([40, 10, 35, 12, 25, 4, 19])
                ->color(Color::Purple),
            Stat::make('Deactivated Account', User::whereStatus(UserEnum::Deactivate)->count())
                ->chart([40, 10, 35, 12, 25, 4, 19])
                ->color(Color::Sky),
            Stat::make('Administrator Account', User::all()->filter(function (User $user) {
                return $user->hasRole('Administrator');
            })->count())
                ->chart([40, 10, 35, 12, 25, 4, 19])
                ->color(Color::Red),
        ];
    }
}
