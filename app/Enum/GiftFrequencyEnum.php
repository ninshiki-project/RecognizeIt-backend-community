<?php

/*
 * Copyright (c) 2025.
 *
 * Filename: GiftFrequency.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum GiftFrequencyEnum: string implements HasLabel
{
    case MONTHLY = 'Monthly';
    case YEARLY = 'Yearly';
    case WEEKLY = 'Weekly';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::MONTHLY => 'Monthly',
            self::YEARLY => 'Yearly',
            self::WEEKLY => 'Weekly',
        };
    }
}
