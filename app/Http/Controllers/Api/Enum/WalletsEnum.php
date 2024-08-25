<?php

namespace App\Http\Controllers\Api\Enum;

enum WalletsEnum: string
{
    case DEFAULT = 'ninshiki-wallet';
    case SPEND = 'spend-wallet';
    case CURRENCY = 'currency-wallet';
}
