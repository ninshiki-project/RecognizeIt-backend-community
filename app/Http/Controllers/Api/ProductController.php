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
use Illuminate\Http\Request;
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
        \Log::info($request->has('status'));
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
     * Create New Product
     *
     * @param ProductRequest $request
     * @return ProductsResource|JsonResponse
     */
    public function store(ProductRequest $request)
    {
        // upload image to the cloudinary
        $fileName = Str::orderedUuid();
        $this->uploadedAsset = $request->image->storeOnCloudinaryAs('posts', $fileName);
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
        }else{
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

    public function update(Request $request, $id) {}

    public function patch(Request $request, $id) {}

    /**
     * Delete Product
     *
     * @param  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $productInUsed = false;

        $product = Products::findOrFail($id);
        // NOTE: Check and make sure that the product will not be deleted if it in use by other module
        if (Wishlist::has($product)) {
            $productInUsed = true;
        }
        // TODO: Add checking if the product is in used in the Shop/Store

        $product->delete();

        return response()->json(status: Response::HTTP_NO_CONTENT);
    }
}
