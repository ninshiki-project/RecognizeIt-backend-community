<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: RedeemController.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Controllers\Api;

use App\Enum\RedeemStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\GetRedeemRequest;
use App\Http\Resources\RedeemResource;
use App\Models\Products;
use App\Models\Redeem;
use App\Models\Shop;
use Bavix\Wallet\Internal\Exceptions\ExceptionInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;
use MarJose123\NinshikiEvent\Events\Shop\UserRedeemFromShop;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class RedeemController extends Controller
{
    /**
     * Get all the Redeem items
     *
     * Display in a list all the redeem items from the shop
     *
     * @param  GetRedeemRequest  $request
     * @return AnonymousResourceCollection
     */
    public function index(GetRedeemRequest $request)
    {
        $redeem = Redeem::query();

        return RedeemResource::collection($redeem->user($request->user)->status($request->status)->fastPaginate());
    }

    /**
     * Redeem Item from Shop
     *
     * @param  Request  $request
     * @return RedeemResource|JsonResponse
     *
     * @throws ExceptionInterface
     */
    public function store(Request $request)
    {
        $request->validate([
            'shop' => [
                'required',
                'exists:shops,id',
                'string',
            ],
        ]);
        $shop = Shop::findOrFail($request->shop);
        $product = $shop->product;

        // check if the product still available
        if (! $product->isAvailable()) {
            return response()->json([
                'message' => 'Product is not available due to unavailable of the stock',
                'success' => false,
            ], HttpResponse::HTTP_BAD_REQUEST);
        }

        // pay the item using the ninshiki-wallet
        /**
         * @status 402
         */
        $userWallet = $request->user()->getWallet('ninshiki-wallet');
        if (! $userWallet->safePay($product)) {
            return response()->json([
                'message' => 'Payment processing failed. Please check your wallet balance and try again.',
                'success' => false,
            ], HttpResponse::HTTP_PAYMENT_REQUIRED);
        }

        $redeem = Redeem::create([
            'shop_id' => $shop->id,
            'user_id' => $request->user()->id,
            'status' => RedeemStatusEnum::WAITING_APPROVAL->value,
            'product_id' => $shop->product->id,
        ]);

        $product->decrement('stock', 1);
        $product->save();

        /**
         * Dispatch event
         */
        UserRedeemFromShop::dispatch($redeem, $request->user(), $shop);

        /**
         * Delete shop record if the Product is not anymore available
         *
         * @var Products $product
         */
        if (! Products::find($product->id)->isAvailable()) {
            /** @var Shop $shop */
            $shop->delete();
        }

        /**
         * @status 201
         */
        return RedeemResource::make($redeem);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return RedeemResource
     */
    public function show(string $id)
    {
        $redeem = Redeem::findOrFail($id);

        return RedeemResource::make($redeem);
    }

    /**
     * Cancel the redeem item
     *
     * @param  string  $id
     * @return JsonResponse
     */
    public function cancel(string $id)
    {
        $redeem = Redeem::findOrFail($id);
        if ($redeem->status != RedeemStatusEnum::WAITING_APPROVAL) {
            return response()->json([
                'message' => 'Unable to canceled redeem due to it is already in process.',
                'success' => false,
            ], HttpResponse::HTTP_FORBIDDEN);
        }

        // revert back the product stock
        $redeem->shop->product->increment('stock', 1);

        // refund the user
        $userWallet = auth()->user()->getWallet('ninshiki-wallet');
        $userWallet->refund($redeem->shop->product);

        $redeem->status = RedeemStatusEnum::CANCELED;
        $redeem->save();

        /**
         * @status 200
         */
        return response()->json([
            'message' => 'Redeem canceled successfully, payment has been refunded.',
            'success' => true,
        ], HttpResponse::HTTP_OK);
    }

    /**
     * Update Redeemed Status
     *
     * @param  Request  $request
     * @param  string  $id
     * @return RedeemResource|JsonResponse
     */
    public function status(Request $request, string $id)
    {
        $request->validate([
            'status' => [
                'required',
                'string',
                Rule::enum(RedeemStatusEnum::class),
            ],
        ]);
        $redeem = Redeem::findOrFail($id);
        if ($redeem->status == RedeemStatusEnum::REDEEMED->value) {
            return response()->json([
                'message' => 'Unable to change the status due to it was already completed',
                'success' => false,
            ], HttpResponse::HTTP_FORBIDDEN);
        }

        // refund if cancel / rejected / declined
        if ($request->status == RedeemStatusEnum::CANCELED->value || $redeem->status == RedeemStatusEnum::DECLINED->value) {
            // revert back the product stock
            $redeem->shop->product->increment('stock', 1);

            // refund the user
            auth()->user()->refund($redeem->shop->product);
            $redeem->status = $request->status;
            $redeem->push();

            /**
             * @status 200
             */
            return response()->json([
                'message' => 'Redeem canceled successfully, payment has been refunded.',
                'success' => true,
            ], HttpResponse::HTTP_OK);
        }

        $redeem->status = $request->status;
        $redeem->push();

        return RedeemResource::make($redeem->refresh());
    }
}
