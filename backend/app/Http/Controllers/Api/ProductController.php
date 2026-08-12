<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'images'])->get();

        return ProductResource::collection($products);
    }

    public function show($id)
    {
        $product = Product::with(['category', 'images'])->findOrFail($id);

        return new ProductResource($product);
    }
}
