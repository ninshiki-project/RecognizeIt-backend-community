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

namespace App\Http\Controllers\Api\Enum;

enum RedeemStatusEnum: string
{
    case REDEEMED = 'Redeemed';
    case APPROVED = 'Approved';
    case DECLINED = 'Declined';
    case PROCESSING = 'Processing';
    case WAITING_APPROVAL = 'Waiting-Approval';
    case CANCELED = 'Canceled';
}
