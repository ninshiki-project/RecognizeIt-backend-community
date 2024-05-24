<?php

namespace App\Http\Controllers\Api\Enum;

enum InvitationCaseEnum: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
}
