<?php

namespace App\Service;

use App\Repositories\ProductRepositoryInterface;

class ProductService
{
    public function __construct(
        private ProductRepositoryInterface $repository
    ) {
    }

    public function all($perPage = 15)
    {
        return $this->repository->all($perPage);
    }

    public function getProduct(string $id)
    {
    return $this->repository->find($id);
    }
}
