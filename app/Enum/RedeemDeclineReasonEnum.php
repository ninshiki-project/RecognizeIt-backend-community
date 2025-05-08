<?php

/*
 * Copyright (c) 2025.
 *
 * Filename: RedeemDeclineReasonEnum.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum RedeemDeclineReasonEnum: string implements HasLabel
{
    case ITEM_TEMPORARILY_UNAVAILABLE = 'Item Temporarily Unavailable';
    case EXCEED_REDEMPTION_LIMIT = 'Exceeded Redemption Limits';
    case INCORRECT_ITEM_PRICING = 'Incorrect Item Pricing or Listing Error';
    case ITEM_NO_LONGER_AVAILABLE = 'Item No Longer Supported';

    public function getDescription(): string
    {
        return match ($this) {
            self::ITEM_TEMPORARILY_UNAVAILABLE => 'The requested item is out of stock or temporarily unavailable in the store.',
            self::EXCEED_REDEMPTION_LIMIT => 'You have reached the maximum redemption limit for the current period (e.g., daily or monthly).',
            self::INCORRECT_ITEM_PRICING => 'The requested item has an incorrect price or was mistakenly listed in the store.',
            self::ITEM_NO_LONGER_AVAILABLE => 'The item you attempted to redeem is no longer available and has been removed from the store.'
        };
    }

    public function getEmailContent(): string
    {
        return match ($this) {
            self::ITEM_TEMPORARILY_UNAVAILABLE => 'We regret to inform you that your redemption request for [$item.name] has been declined because the item is currently unavailable. Please check back soon for updates on restocking.',
            self::EXCEED_REDEMPTION_LIMIT => 'Your redemption request for [$item.name] has been declined because you have exceeded your redemption limit and to give other chance. Please try again later.',
            self::INCORRECT_ITEM_PRICING => 'Your redemption request for [$item.name] has been declined because of a pricing or listing error. Please check the updated store listing and try again.',
            self::ITEM_NO_LONGER_AVAILABLE => 'Your redemption request for [$item.name] has been declined because the item is no longer supported. Please browse the store for available alternatives.'
        };

    }

    public function getLabel(): string
    {
        return match ($this) {
            self::ITEM_TEMPORARILY_UNAVAILABLE => self::ITEM_TEMPORARILY_UNAVAILABLE->value,
            self::EXCEED_REDEMPTION_LIMIT => self::EXCEED_REDEMPTION_LIMIT->value,
            self::INCORRECT_ITEM_PRICING => self::INCORRECT_ITEM_PRICING->value,
            self::ITEM_NO_LONGER_AVAILABLE => self::ITEM_NO_LONGER_AVAILABLE->value,
        };
    }
}
