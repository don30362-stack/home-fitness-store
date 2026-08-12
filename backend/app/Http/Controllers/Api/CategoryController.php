<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    public function index()
    {
        $categories =  Category::with('children')->whereNull('parent_id')->get();
        return CategoryResource::collection($categories);
    }
}
