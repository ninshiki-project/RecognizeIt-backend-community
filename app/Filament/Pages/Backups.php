<?php

/*
 * Copyright (c) 2025.
 *
 * Filename: Backups.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Filament\Pages;

use App\Filament\Resources\PostingLimitResource;
use Illuminate\Contracts\Support\Htmlable;
use ShuvroRoy\FilamentSpatieLaravelBackup\Pages\Backups as BaseBackups;

class Backups extends BaseBackups
{
    protected static ?string $navigationParentItem = 'Settings';

    public function getHeading(): string|Htmlable
    {
        return 'Application Backups';
    }

    public static function getNavigationGroup(): ?string
    {
        return '';
    }

    public static function getNavigationLabel(): string
    {
        return 'System Backups';
    }

    public static function getNavigationSort(): ?int
    {
        return PostingLimitResource::getNavigationSort() + 1;
    }
}
