<?php
/*
 * Copyright (c) 2024.
 *
 * Filename: ProductStatusEnum.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Controllers\Api\Enum;

enum ProductStatusEnum: string
{
    case AVAILABLE = 'available';
    case UNAVAILABLE = 'unavailable';
}
