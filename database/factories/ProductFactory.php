<?php

namespace Database\Factories;

use App\Enum\ProductStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Nonstandard\Uuid;
use Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
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
        $name = fake()->randomElement(['Laptop', 'Headphones', 'Camera', 'Smartwatch']);
        return [
            'id' => Uuid::uuid7(),
            'name' => $name,
            'sku' => fake()->unique()->numerify('PRD-#####'),
            'description' => fake()->paragraph(),
            'price' => fake()->numberBetween(100, 2000),
            'stock_quantity' => fake()->numberBetween(100, 500),
            'status' => $this->status(ProductStatus::Active),
        ];
    }

    public function status(ProductStatus $status): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => $status,
            'stock_quantity' => match ($status) {
                ProductStatus::InActive => 0,
                default => $attributes['stock_quantity']
            }
        ]);
    }

}
