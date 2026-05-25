<?php

namespace App\Service;

use App\Repositories\ProductRepository;
use Str;

class ProductService
{
    public function __construct(
        private ProductRepository $repository
    ) {
    }

    public function all($perPage = 15)
    {
        return $this->repository->all($perPage);
    }

    public function lowStock($perPage = 15)
    {
        return $this->repository->lowStock($perPage);
    }

    public function getProduct(string $id)
    {
        return $this->repository->find($id);
    }

    public function createProduct(array $data)
    {
        $data['sku'] = Str::replace(' ', '_', Str::upper($data['name'])) . '-' . Str::random(8);
        return $this->repository->create($data);
    }
    public function updateProduct(string $id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function updateProductStock(string $id, array $data)
    {
        return $this->repository->update($id, $data);
    }
}
