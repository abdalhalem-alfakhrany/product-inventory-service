<?php

use App\Enum\ProductStatus;
use App\Models\Product;
use function Pest\Laravel\assertDatabaseCount;

it('can list all products', function () {
    Product::factory()->status(ProductStatus::Active)->count(100)->create();
    $response = $this->getJson('/api/products/');
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'data',
        'meta' => [
            'pagination' => [
                'current_page'
            ]
        ]
    ]);
});

it('can get one product', function () {
    Product::factory()->status(ProductStatus::Active)->create();
    $uuid = Product::first()->id;
    $response = $this->getJson("/api/products/$uuid");
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'data',
        'meta'
    ]);
});

it('can create new product', function () {
    $response = $this->postJson('/api/products', [
        'name' => 'new product',
        'description' => 'new product description',
        'price' => 100,
        'stock_quantity' => 10,
    ]);
    assertDatabaseCount('products',1);
    $response->assertStatus(200);
});
