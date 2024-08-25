<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Enum\WalletsEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 *  Wallet - Ninshiki
 */
class WalletController extends Controller
{
    /**
     * Get Ninshiki Wallet Balance
     *
     * Available balance of the wallet. The balance can be used to purchase in the shop or exchange it into a real-currency
     *
     * @return JsonResponse
     */
    public function defaultWalletBalance()
    {
        $wallet = auth()->user()->getWallet(WalletsEnum::DEFAULT->value);

        /**
         * @status 200
         */
        return response()->json([
            'success' => true,
            /**
             * The available balance of the wallet
             *
             * @var int
             */
            'balance' => $wallet->balanceInt,
        ]);
    }

    /**
     * Get Spend Wallet Balance
     *
     * Available balance of the wallet. The balance can be used to recognize or sending a tip to the other user
     *
     * @return JsonResponse
     */
    public function spendWalletBalance()
    {
        $wallet = auth()->user()->getWallet(WalletsEnum::SPEND->value);

        /**
         * @status 200
         */
        return response()->json([
            'success' => true,
            /**
             * The available balance of the wallet
             *
             * @var int
             */
            'balance' => $wallet->balanceInt,
        ]);
    }

    /**
     * Get Currency Wallet Balance
     *
     * Available balance of the wallet (real-currency). These were all your converted Ninshiki Coins.
     *
     * @return JsonResponse
     */
    public function currencyWalletBalance()
    {
        $wallet = auth()->user()->getWallet(WalletsEnum::CURRENCY->value);

        /**
         * @status 200
         */
        return response()->json([
            'success' => true,
            /**
             * The available balance of the wallet
             *
             * @var int
             */
            'balance' => $wallet->balanceInt,
        ]);
    }
}
