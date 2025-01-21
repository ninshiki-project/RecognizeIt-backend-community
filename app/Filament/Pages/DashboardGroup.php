<?php

namespace App\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Contracts\Support\Htmlable;

class DashboardGroup extends Page
{
    use HasPageShield;

    protected static string $view = 'filament.pages.dashboard-group';

    protected static ?string $title = 'Dashboard';

    protected static string $routePath = '/';

    protected static ?int $navigationSort = -1;

    public function mount(): void
    {
        $this->redirect(GeneralDashboard::getUrl());
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-panels::pages/dashboard.title');
    }

    public static function getNavigationIcon(): string|Htmlable|null
    {
        return static::$navigationIcon
            ?? FilamentIcon::resolve('panels::pages.dashboard.navigation-item')
            ?? (Filament::hasTopNavigation() ? 'heroicon-m-home' : 'heroicon-o-home');
    }
}
