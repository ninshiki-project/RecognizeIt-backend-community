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

use App\Enum\ProductStatusEnum;
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

    public function __construct(public CloudinaryEngine $cloudinary) {}

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
        $this->uploadedAsset = $request->image->storeOnCloudinaryAs('products', $fileName);
        $result = Products::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'cloudinary_id' => $this->uploadedAsset->getPublicId(),
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
     * @param  string  $id
     * @return ProductsResource
     */
    public function show(string $id)
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
     * @param  string  $id
     * @return ProductsResource|JsonResponse
     */
    public function update(ProductRequest $request, string $id)
    {
        if ($request->has('image')) {
            $fileName = Str::orderedUuid();
            $this->uploadedAsset = $request->image->storeOnCloudinaryAs('posts', $fileName);
        }
        $product = Products::findOrFail($id);
        $oldCloudinaryId = $product->cloudinary_id;
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
            if ($oldCloudinaryId) {
                $this->cloudinary->destroy($oldCloudinaryId);
            }

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
     * @param  string  $id
     * @return JsonResponse|\Illuminate\Http\Response
     */
    public function destroy(string $id)
    {
        $product = Products::findOrFail($id);

        if ($product->shop()->exists() || $product->redeems()->exists()) {
            return response()->json([
                'message' => 'Unable to delete product as it is still in use',
                'success' => false,
            ], Response::HTTP_FORBIDDEN);
        }

        // Delete image file in the cloudinary
        if ($product->cloudinary_id) {
            $this->cloudinary->destroy($product->cloudinary_id);
        }

        $product->delete();

        return response()->noContent();
    }
}
