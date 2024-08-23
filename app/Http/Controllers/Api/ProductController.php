<?php

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
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Wishlist;

class ProductController extends Controller
{
    private CloudinaryEngine $uploadedAsset;

    /**
     * Get all product
     *
     * Get all the products that are available and unavailable
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

        return ProductsResource::collection($products->fastPaginate());
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
        $fileName = Str::orderedUuid().'.'.$request->file('image')->getClientOriginalExtension();
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

        return new ProductsResource($product);
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
        if ($request->hasFile('image')) {
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
                'status' => ProductStatusEnum::AVAILABLE->value,
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

        if (Wishlist::has($product)) {
            $productInUsed = true;
        }

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
