<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: RedeemStatusEnum.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Enum;

use Filament\Support\Contracts\HasIcon;

enum RedeemStatusEnum: string implements HasIcon
{
    case REDEEMED = 'Redeemed';
    case APPROVED = 'Approved';
    case DECLINED = 'Declined';
    case PROCESSING = 'Processing';
    case AWAITING_APPROVAL = 'Awaiting Approval';
    case CANCELED = 'Canceled';

    public function getIcon(): string
    {
        return match ($this) {
            self::REDEEMED => 'heroicon-o-receipt-percent',
            self::APPROVED => 'heroicon-o-hand-thumb-up',
            self::DECLINED => 'heroicon-o-hand-thumb-down',
            self::PROCESSING => 'heroicon-o-truck',
            self::AWAITING_APPROVAL => 'heroicon-o-cube',
            self::CANCELED => 'heroicon-o-cube-transparent',
        };
    }
}
