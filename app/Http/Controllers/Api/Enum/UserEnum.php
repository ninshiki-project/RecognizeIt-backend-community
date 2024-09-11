<?php

namespace App\Http\Controllers\Api\Enum;

enum UserEnum: string
{
    case Invited = 'invited';
    case Active = 'active';
    case Inactive = 'inactive';
    case Ban = 'banned';
}
