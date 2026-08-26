<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()
            ->with(['category', 'images'])
            ->where('status', 'active');

        if ($request->filled('category_id')) {
            $category = Category::query()
                ->whereKey($request->integer('category_id'))
                ->whereNotNull('parent_id')
                ->where('status', 'active')
                ->first();

            if (!$category) {
                return response()->json([
                    'message' => '商品分類不存在',
                ], 404);
            }

            $products->where('category_id', $category->id);
        }

        if ($request->filled('parent_category_id')) {
            $parentCategory = Category::query()
                ->whereKey($request->integer('parent_category_id'))
                ->whereNull('parent_id')
                ->where('status', 'active')
                ->first();

            if (!$parentCategory) {
                return response()->json([
                    'message' => '商品分類不存在',
                ], 404);
            }

            $products->whereHas('category', function ($query) use ($parentCategory) {
                $query->where('parent_id', $parentCategory->id);
            });
        }

        return ProductResource::collection($products->get());
    }

    public function show($id)
    {
        $product = Product::with(['category', 'images'])->find($id);

        if (!$product) {
            return response()->json([
                'message' => '商品不存在'
            ], 404);
        }

        return new ProductResource($product);
    }
}
