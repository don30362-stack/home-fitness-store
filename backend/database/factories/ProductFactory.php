<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_code' => fake()->unique()->numerify('PRD-#####'),
            'name' => fake()->words(3, true),
            'price' => fake()->numberBetween(500, 30000),
            'short_description' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'stock' => fake()->numberBetween(0, 100),
            'low_stock_threshold' => 5,
            'status' => 'active',
        ];
    }
}
