<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    public function index()
    {
        $categories =  Category::query()
            ->whereNull('parent_id')
            ->where('status', 'active')
            ->with([
                'children' => function ($query) {
                    $query
                        ->where('status', 'active')
                        ->orderBy('sort_order')
                        ->orderBy('id');
                }
            ])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
        return CategoryResource::collection($categories);
    }
}
