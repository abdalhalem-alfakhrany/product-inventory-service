<?php

use App\Enum\ProductStatus;
use App\Models\Product;
use function Pest\Laravel\assertDatabaseCount;

it('list all products with pagination', function () {
    Product::factory()->status(ProductStatus::Active)->count(10)->create();
    $response = $this->getJson('/api/products/');
    $response->assertStatus(200)->assertJson(['success' => true]);
});
