<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Enum\ProductStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\GetProductRequest;
use App\Http\Resources\ProductsResource;
use App\Models\Products;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Wishlist;

class ProductController extends Controller
{
    /**
     * Get all product
     *
     * Get all the products that are available and unavailable
     */
    public function index(GetProductRequest $request)
    {
        $products = Products::query();
        if ($request->status === ProductStatusEnum::AVAILABLE) {
            $products->available();
        } else {
            $products->unavailable();
        }

        return ProductsResource::collection($products->fastPaginate());
    }

    public function store(Request $request) {}

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
