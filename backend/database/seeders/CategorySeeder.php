<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //第一層分類
        $categoryA = Category::create([
            'parent_id' => null,
            'name' => '測試分類 A',
            'status' => 'active',
            'sort_order' => 1,
        ]);

        $categoryB = Category::create([
            'parent_id' => null,
            'name' => '測試分類 B',
            'status' => 'active',
            'sort_order' => 2,
        ]);

        //第二層分類
        Category::create([
            'parent_id' => $categoryA->id,
            'name' => '測試分類 A-1',
            'status' => 'active',
            'sort_order' => 1,
        ]);

        Category::create([
            'parent_id' => $categoryA->id,
            'name' => '測試分類 A-2',
            'status' => 'active',
            'sort_order' => 2,
        ]);

        Category::create([
            'parent_id' => $categoryB->id,
            'name' => '測試分類 B-1',
            'status' => 'active',
            'sort_order' => 1,
        ]);

        Category::create([
            'parent_id' => $categoryB->id,
            'name' => '測試分類 B-2',
            'status' => 'active',
            'sort_order' => 2,
        ]);
    }
}
