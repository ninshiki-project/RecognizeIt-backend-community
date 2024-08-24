<?php

namespace App\Http\Controllers\Api\Enum;

enum ProductStatusEnum: string
{
    case AVAILABLE = 'available';
    case UNAVAILABLE = 'unavailable';
}
