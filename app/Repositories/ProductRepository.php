<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;

class ProductRepository implements ProductRepositoryInterface
{
    public function all(int $perPage = 15): LengthAwarePaginator
    {
        return QueryBuilder::for(Product::class)
        ->allowedFilters(['name'])
            ->paginate($perPage);
    }

    public function create(array $data): ?Product
    {
        return Product::create($data);
    }

    public function update(array $data, int $id): int
    {
        $product = Product::findOrFail($id);
        return $product->update($data);
    }

    public function delete(int $id): bool
    {
        $product = Product::findOrFail($id);
        return $product->delete();
    }

    public function find(int $id): ?Product
    {
        return Product::findOrFail($id);
    }
}
