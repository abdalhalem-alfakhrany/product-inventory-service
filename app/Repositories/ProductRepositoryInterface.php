<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function all(int $page, int $perPage): LengthAwarePaginator;

    public function create(array $data): ?Product;

    public function update(string $id, array $data): ?Product;

    public function delete(string $id): bool;

    public function lowStock(int $perPage = 15): LengthAwarePaginator;

    public function find(string $id): ?Product;
}
