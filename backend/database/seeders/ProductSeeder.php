<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::whereNotNull('parent_id')->get();

        for($i = 0; $i < 24; $i++) {
            Product::factory()->create([
                'category_id' => $categories->random()->id,
            ]);
        }
    }
}
