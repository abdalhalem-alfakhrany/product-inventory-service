<?php

use App\Enum\ProductStatus;
use App\Models\Product;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

it('can list all products', function () {
    Product::factory()->status(ProductStatus::Active)->count(100)->create();
    $response = $this->getJson('/api/products/');
    $response->assertOk();
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
    $response->assertSuccessful();
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
    assertDatabaseCount('products', 1);
    $response->assertCreated();
    $response->assertJsonStructure([
        'success',
        'data',
        'meta'
    ]);
});

it('can update product', function () {
    Product::factory()->status(ProductStatus::Active)->count(1)->create();
    $uuid = Product::first()->id;

    $response = $this->putJson("/api/products/$uuid", [
        'name' => 'updated product name',
        'description' => 'updated product description',
    ]);

    $response->assertSuccessful();
    $response->assertJsonStructure([
        'success',
        'data',
        'meta'
    ]);
    assertDatabaseHas('products', [
        'id' => $uuid,
        'name' => 'updated product name',
        'description' => 'updated product description'
    ]);
});

it('can delete product', function () {
    Product::factory()->status(ProductStatus::Active)->create();
    $uuid = Product::first()->id;

    $response = $this->deleteJson("/api/products/$uuid");
    $response->assertStatus(204);
    assertDatabaseCount('products', 0);
});

it('can update product stock quantity', function () {
    Product::factory()->status(ProductStatus::Active)->count(1)->create();
    $uuid = Product::first()->id;

    $response = $this->postJson("/api/products/$uuid/stock", ['stock_quantity' => 5]);
    $response->assertSuccessful();
    $response->assertJsonStructure([
        'success',
        'data',
        'meta'
    ]);

    assertDatabaseHas('products', [
        'id' => $uuid,
        'stock_quantity' => 5
    ]);
});

it('can list product with stock quantity below threshold', function () {
    $response = $this->getJson('/api/products/low-stock');
    $response->assertOk();
});
