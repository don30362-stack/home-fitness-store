<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;

class ProductImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();

        foreach ($products as $product){
            //主圖
            ProductImage::factory()->create([
                'product_id' => $product->id,
                'is_primary' => true,
                'sort_order' => 1,
            ]);

            //其他圖片
            ProductImage::factory()->count(2)->create([
                'product_id' => $product->id,
                'is_primary' => false,
            ])->each(function ($image, $index) {
                $image->update([
                    'sort_order' => $index + 2,
                ]);
            });
        }
    }
}
