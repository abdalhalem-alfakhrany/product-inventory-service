<?php

use App\Enum\ProductStatus;
use App\Models\Product;

it('list all products with pagination', function () {
    Product::factory()->status(ProductStatus::Active)->count(20)->create();
    $response = $this->getJson('/api/products/');
    $response->assertStatus(200);
    $response->assertJsonPath('success', true);
    $response->assertJsonCount(20, 'data');
});
