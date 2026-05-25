<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function all(int $perPage): LengthAwarePaginator;

    public function create(array $data): ?Product;

    public function update(string $id, array $data): bool;

    public function delete(int $id): bool;

    public function find(string $id): ?Product;
}
