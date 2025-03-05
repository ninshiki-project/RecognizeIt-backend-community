<?php

namespace App\Filament\Pages;

use App\Filament\Resources\DepartmentsResource;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;

class CompanySettingGroup extends Page
{
    use HasPageShield;

    /**
     * This page will be used only to group other settings pages/resources
     */
    protected static ?string $title = 'Company';

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static string $view = 'filament.pages.settings-group';

    protected static ?int $navigationSort = 2;

    public function mount(): void
    {
        $this->redirect(DepartmentsResource::getUrl());
    }
}
