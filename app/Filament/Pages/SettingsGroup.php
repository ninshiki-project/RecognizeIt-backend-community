<?php

namespace App\Filament\Pages;

use App\Filament\Resources\PostingLimitResource;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use ninshikiProject\GeneralSettings\Pages\GeneralSettingsPage;

class SettingsGroup extends Page
{
    use HasPageShield;

    /**
     * This page will be used only to group other settings pages/resources
     */
    protected static ?string $title = 'Settings';

    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';

    protected static string $view = 'filament.pages.settings-group';

    protected static ?int $navigationSort = 5;

    public function mount(): void
    {
        $this->redirect(GeneralSettingsPage::getUrl());
    }
}
