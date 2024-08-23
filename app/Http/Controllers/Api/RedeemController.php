<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Enum\RedeemStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\GetRedeemRequest;
use App\Http\Resources\RedeemResource;
use App\Models\Redeem;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

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
     * @return RedeemResource
     */
    public function store(Request $request)
    {
        $request->validate([
            'shop' => [
                'required',
                'exists:shops,id',
            ],
        ]);
        $shop = Shop::query()->findOrFail($request->shop);
        $redeem = Redeem::create([
            'shop_id' => $shop?->id,
            'user_id' => $request->user()->id,
            'status' => RedeemStatusEnum::WAITING_APPROVAL->value,
            'product_id' => $shop?->product?->id,
        ]);

        return RedeemResource::make($redeem);
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return Response
     */
    public function show($id) {}

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  $id
     * @return Response
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return Response
     */
    public function destroy($id) {}
}
