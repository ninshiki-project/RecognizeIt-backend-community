<?php
/*
 * Copyright (c) 2024.
 *
 * Filename: ProductController.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Enum\ProductStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\GetProductRequest;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductsResource;
use App\Models\Products;
use App\Models\Scopes\ProductAvailableScope;
use CloudinaryLabs\CloudinaryLaravel\CloudinaryEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    private CloudinaryEngine $uploadedAsset;

    protected static string $cacheKey = 'products';

    /**
     * Get all product
     *
     * Get all the products that are available and unavailable
     *
     * @return AnonymousResourceCollection<LengthAwarePaginator<ProductsResource>>
     */
    public function index(GetProductRequest $request)
    {
        $products = Products::query();
        if ($request->has('status')) {
            if ($request->status === ProductStatusEnum::AVAILABLE->value) {
                $products->available();
            } else {
                $products->withoutGlobalScope(new ProductAvailableScope)->unavailable();
            }
        }

        return Cache::flexible(static::$cacheKey, [5, 10], function () use ($products) {
            return ProductsResource::collection($products->paginate());
        });

    }

    /**
     * Create Product
     *
     * @param  ProductRequest  $request
     * @return ProductsResource|JsonResponse
     */
    public function store(ProductRequest $request)
    {
        // upload image to the cloudinary
        $fileName = Str::orderedUuid();
        $this->uploadedAsset = $request->image->storeOnCloudinaryAs('product', $fileName);
        $result = Products::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $this->uploadedAsset->getSecurePath(),
            'status' => ProductStatusEnum::AVAILABLE->value,
            'stock' => $request->stock,
        ]);

        if ($result) {
            /**
             * @status 201
             */
            return ProductsResource::make($result);
        } else {
            return response()->json([
                'message' => 'Product not created',
                'success' => false,
            ], 400);
        }
    }

    /**
     * Show Product
     *
     * @param  $id
     * @return ProductsResource
     */
    public function show($id)
    {
        $product = Products::findOrFail($id);

        return Cache::flexible(static::$cacheKey.$id, [5, 10], function () use ($product) {
            return new ProductsResource($product);
        });
    }

    /**
     *  Update Product
     *
     *  Update product its information by bulk column or single column
     *
     * @param  ProductRequest  $request
     * @param  $id
     * @return ProductsResource|JsonResponse
     */
    public function update(ProductRequest $request, $id)
    {
        if ($request->has('image')) {
            $fileName = Str::orderedUuid();
            $this->uploadedAsset = $request->image->storeOnCloudinaryAs('posts', $fileName);
        }
        $product = Products::findOrFail($id);
        $result = $product->update([
            ...($request->name ? [
                'name' => $request->name,
            ] : []),
            ...($request->description ? [
                'description' => $request->description,
            ] : []),
            ...($request->price ? [
                'price' => $request->price,
            ] : []),
            ...($request->hasFile('image') ? [
                'image' => $this->uploadedAsset->getSecurePath(),
            ] : []),
            ...($request->stock ? [
                'stock' => $request->stock,
            ] : []),
            ...($request->status ? [
                'status' => $request->status,
            ] : []),
        ]);
        if (! $result) {
            return response()->json([
                'message' => 'Product not updated',
                'success' => false,
            ], Response::HTTP_NOT_MODIFIED);
        }

        return ProductsResource::make($product->refresh());
    }

    /**
     * Delete Product
     *
     * @param  $id
     * @return JsonResponse|\Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $productInUsed = false;

        $product = Products::findOrFail($id);

        if ($product->shop()->count() > 0) {
            $productInUsed = true;
        }

        if ($productInUsed) {
            return response()->json([
                'message' => 'Unable to delete product as it is still in use',
                'success' => false,
            ], Response::HTTP_FORBIDDEN);
        }

        $product->delete();

        return response()->noContent();
    }
}
