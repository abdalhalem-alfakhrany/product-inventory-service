<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function all(): LengthAwarePaginator;

    public function create(array $data): ?Product;

    public function update(array $data, int $id): int;

    public function delete(int $id): bool;

    public function find(int $id): ?Product;
}
