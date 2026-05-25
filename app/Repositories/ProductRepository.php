<?php

namespace App\Repositories;

use App\Exceptions\ProductNotFoundException;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;

class ProductRepository implements ProductRepositoryInterface
{
    public function all(int $page, int $perPage = 15): LengthAwarePaginator
    {
        return QueryBuilder::for(Product::class)
            ->allowedFilters(['name', 'sku', 'price'])
            ->allowedSorts(['name', 'sku', 'price'])
            ->paginate(perPage: $perPage, page: $page);
    }

    public function create(array $data): ?Product
    {
        return Product::create($data);
    }

    public function update(string $id, array $data): ?Product
    {
        $product = Product::find($id) ?? throw new ProductNotFoundException();
        $product->update($data);
        return $product->fresh();
    }

    public function delete(string $id): bool
    {
        $product = Product::find($id) ?? throw new ProductNotFoundException();
        return $product->delete();
    }

    public function find(string $id): ?Product
    {
        return Product::find($id) ?? throw new ProductNotFoundException();
    }

    public function lowStock(int $perPage = 15): LengthAwarePaginator
    {
        return QueryBuilder::for(Product::class)
            ->lowStock()
            ->allowedFilters(['name', 'sku', 'price'])
            ->allowedSorts(['name', 'sku', 'price'])
            ->paginate($perPage);
    }
}
