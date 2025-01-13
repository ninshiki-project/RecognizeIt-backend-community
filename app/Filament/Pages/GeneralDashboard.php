<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DashboardStatsOverview;
use Filament\Pages\Dashboard;
use Orion\FilamentGreeter\GreeterWidget;

class GeneralDashboard extends Dashboard
{
    protected static string $routePath = 'dashboard';

    protected static ?string $navigationParentItem = 'Dashboard';

    public function getWidgets(): array
    {
        return [
            GreeterWidget::class,
            DashboardStatsOverview::class,
        ];
    }
}
