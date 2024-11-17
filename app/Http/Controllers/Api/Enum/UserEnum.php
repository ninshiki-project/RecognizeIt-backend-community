<?php
/*
 * Copyright (c) 2024.
 *
 * Filename: UserEnum.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Controllers\Api\Enum;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum UserEnum: string implements HasColor, HasIcon, HasLabel
{
    case Invited = 'invited';
    case Active = 'active';
    case Inactive = 'inactive';
    case Ban = 'banned';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Invited => Color::Orange,
            self::Active => Color::Green,
            self::Inactive => Color::Gray,
            self::Ban => Color::Red,
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Invited => 'heroicon-o-envelope',
            self::Active => 'heroicon-o-user',
            self::Inactive => 'heroicon-o-arrow-trending-down',
            self::Ban => 'heroicon-o-x-circle',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Invited => 'Invited',
            self::Active => 'Active',
            self::Inactive => 'Inactive',
            self::Ban => 'Banned',
        };
    }
}
