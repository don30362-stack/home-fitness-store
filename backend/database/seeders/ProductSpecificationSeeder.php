<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSpecificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();

        foreach ($products as $index => $product) {
            $product->specifications()->createMany([
                [
                    'spec_name' => '材質',
                    'spec_value' => '高強度鋼材',
                    'sort_order' => 1,
                ],
                [
                    'spec_name' => '重量',
                    'spec_value' => (10 + $index) . ' kg',
                    'sort_order' => 2,
                ],
                [
                    'spec_name' => '尺寸',
                    'spec_value' => '50 × 30 × 20 cm',
                    'sort_order' => 3,
                ],
            ]);
        }
    }
}
