<?php

namespace App\Http\Controllers\Api;

use App\Events\ShopAdded;
use App\Http\Controllers\Controller;
use App\Http\Resources\ShopResource;
use App\Models\Shop;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShopController extends Controller
{
    /**
     * Get all shop products
     */
    public function index()
    {
        return ShopResource::collection(Shop::all());
    }

    /**
     * Add product to the shop
     *
     * @param  Request  $request
     * @return ShopResource|JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'product' => [
                'required',
                'string',
                'exists:products,id',
            ],
        ]);

        if ($request->product && Shop::where('product_id', $request->product)->exists()) {

            /**
             * Return if the product is already existing in the shop
             *
             * @response array{message: string, success: bool}
             *
             * @status 403
             */
            return response()->json([
                'message' => 'Product already exists in the shop',
                'success' => false,
            ], Response::HTTP_FORBIDDEN);
        }

        $shop = Shop::create([
            'product_id' => $request->product,
        ]);

        /**
         * Dispatch an event
         */
        ShopAdded::dispatch($shop);

        /**
         * The product added to shop
         *
         * @status 201
         */
        return ShopResource::make($shop);

    }

    /**
     * Remove Product to Shop
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Shop::destroy($id);

        return response()->noContent();
    }
}
