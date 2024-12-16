<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ShopGroup extends Page
{
    /**
     * This page will be used only to group other settings pages/resources
     */
    protected static ?string $title = 'Store';

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static string $view = 'filament.pages.settings-group';

    protected static ?int $navigationSort = 4;
}
