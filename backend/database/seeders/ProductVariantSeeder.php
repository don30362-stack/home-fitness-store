<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductVariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::orderBy('id')->take(3)->get();

        if ($products->count() < 3) {
            return;
        }

        // 商品 1：有規格，包含正常庫存與缺貨
        $product1 = $products[0];

        $product1->update([
            'stock' => null,
        ]);

        $product1->variants()->createMany([
            [
                'option_name' => '顏色',
                'option_value' => '黑色',
                'stock' => 10,
                'status' => 'active',
            ],
            [
                'option_name' => '顏色',
                'option_value' => '灰色',
                'stock' => 0,
                'status' => 'active',
            ],
        ]);

        // 商品 2：有規格，包含低庫存
        $product2 = $products[1];

        $product2->update([
            'stock' => null,
        ]);

        $product2->variants()->createMany([
            [
                'option_name' => '顏色',
                'option_value' => '黑色',
                'stock' => 3,
                'status' => 'active',
            ],
            [
                'option_name' => '顏色',
                'option_value' => '紅色',
                'stock' => 8,
                'status' => 'active',
            ],
        ]);

        // 商品 3：有規格，包含停用規格
        $product3 = $products[2];

        $product3->update([
            'stock' => null,
        ]);

        $product3->variants()->createMany([
            [
                'option_name' => '顏色',
                'option_value' => '黑色',
                'stock' => 12,
                'status' => 'active',
            ],
            [
                'option_name' => '顏色',
                'option_value' => '金色',
                'stock' => 5,
                'status' => 'inactive',
            ],
        ]);
    }
}
