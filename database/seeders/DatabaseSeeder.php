<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Enum\ProductStatus;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Product::factory()->status(ProductStatus::Active)->count(10)->create();
        Product::factory()->status(ProductStatus::InActive)->count(10)->create();
    }
}
