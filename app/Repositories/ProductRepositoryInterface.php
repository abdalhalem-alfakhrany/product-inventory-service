<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    public function all(): Collection;

    public function create(array $data): ?Product;

    public function update(array $data, int $id): int;

    public function delete(int $id): bool;

    public function find(int $id): ?Product;
}
