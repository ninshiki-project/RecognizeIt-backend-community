<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShopResource;
use App\Models\Shop;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
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
        return Cache::flexible(static::$cacheKey, [5, 10], function () {
            return ShopResource::collection(Shop::all());
        });
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
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Shop::destroy($id);

        return response()->noContent();
    }
}
