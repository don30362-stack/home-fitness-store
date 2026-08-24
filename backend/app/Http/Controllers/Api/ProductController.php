<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()
            ->with(['category', 'images'])
            ->where('status', 'active');
        
        if ($request->filled('category_id')) {
            $products->where('category_id', $request->category_id);
        }

        if ($request->filled('parent_category_id')) {
            $products->whereHas('category', function ($query) use ($request) {
                $query->where('parent_id', $request->parent_category_id);
            });
        }

        return ProductResource::collection($products->get());
    }

    public function show($id)
    {
        $product = Product::with(['category', 'images'])->findOrFail($id);

        return new ProductResource($product);
    }
}
