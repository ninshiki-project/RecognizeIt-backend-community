<?php

/*
 * Copyright (c) 2025.
 *
 * Filename: RedeemDecline.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Filament\Actions;

use App\Http\Controllers\Api\Enum\RedeemStatusEnum;
use App\Mail\RedeemDeclinedMail;
use App\Models\Redeem;
use Bavix\Wallet\Internal\Exceptions\ExceptionInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class RedeemDeclined
{
    /**
     * @throws ExceptionInterface
     */
    public function handle(Redeem $record, array $data): void
    {
        // update the record status and other column
        $record->status = RedeemStatusEnum::DECLINED;
        $record->decline_reason_category = $data['category'];
        $record->decline_reason = $data['description'];
        $record->declined_at = Carbon::now();
        $record->save();
        $record->refresh();
        // refund the item
        $userWallet = $record->user->getWallet('ninshiki-wallet');
        $userWallet->refund($record->product);
        // send notification to the user who redeem the item
        Mail::to($record->user->email)
            ->send(new RedeemDeclinedMail($record));
    }
}
