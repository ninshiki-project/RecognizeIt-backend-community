<?php

namespace App\Http\Controllers\Api\Enum;

enum PostTypeEnum: string
{
    case System = 'system';
    case User = 'user';
}
