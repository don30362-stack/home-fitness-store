<?php

namespace Database\Factories;

use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductImage>
 */
class ProductImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'image_path' => 'products/' . fake()->uuid() . '.jpg',
            'image_type' => 'gallery',
            'is_primary' => false,
            'sort_order' => 0,
        ];
    }
}
