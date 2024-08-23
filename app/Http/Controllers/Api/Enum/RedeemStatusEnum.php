<?php

namespace App\Http\Controllers\Api\Enum;

enum RedeemStatusEnum: string
{
    case REDEEMED = 'Redeemed';
    case APPROVED = 'Approved';
    case DECLINED = 'Declined';
    case PROCESSING = 'Processing';
    case WAITING_APPROVAL = 'Waiting Approval';
}
