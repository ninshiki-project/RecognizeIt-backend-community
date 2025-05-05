<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: ShopController.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShopResource;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use MarJose123\NinshikiEvent\Events\Shop\NewProductAddedToShop;
use Symfony\Component\HttpFoundation\Response;

class ShopController extends Controller
{
    protected static string $cacheKey = 'shops';

    /**
     * Get all shop products
     *
     * @return AnonymousResourceCollection<LengthAwarePaginator<ShopResource>>
     */
    public function index(): AnonymousResourceCollection
    {
        return ShopResource::collection(Shop::with('favorites')->orderByDesc('created_at')->get());
    }

    /**
     * Add/Remove Shop to User Wishlist
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function toggleShopWishlist(Request $request): JsonResponse
    {
        $request->validate([
            'shop' => [
                'required',
                'string',
                'exists:shops,id',
            ],
            // If the user is not provided, the user session will be used instead.
            'user' => ['nullable', 'string', 'exists:users,id'],
        ]);
        if (! $request->has('user')) {
            $request->merge(['user' => auth()->user()->id]);
        }

        $user = User::find($request->user);
        /** @var User $user */
        $user->toggleFavorite(Shop::find($request->shop));

        return response()->json([
            'message' => 'Shop added to wishlist',
            'success' => true,
        ]);

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
        NewProductAddedToShop::dispatch($shop);

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
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id)
    {
        Shop::destroy($id);

        return response()->noContent();
    }
}
