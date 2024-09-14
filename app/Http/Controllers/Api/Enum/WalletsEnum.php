<?php
/*
 * Copyright (c) 2024.
 *
 * Filename: WalletsEnum.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Controllers\Api\Enum;

enum WalletsEnum: string
{
    case DEFAULT = 'ninshiki-wallet';
    case SPEND = 'spend-wallet';
    case CURRENCY = 'currency-wallet';
}
