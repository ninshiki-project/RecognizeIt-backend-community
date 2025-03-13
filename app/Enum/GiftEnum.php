<?php

/*
 * Copyright (c) 2025.
 *
 * Filename: GiftEnum.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum GiftEnum: string implements HasLabel
{
    case COINS = 'coins';
    case SHOP = 'shop';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::SHOP => self::SHOP->name,
            self::COINS => self::COINS->name,
        };
    }
}
