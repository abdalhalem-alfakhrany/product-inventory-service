<?php

namespace App\Service;

use App\Repositories\ProductRepositoryInterface;

class ProductService
{
    public function __construct(
        private ProductRepositoryInterface $repository
    ) {
    }

    public function all()
    {
        return $this->repository->all();
    }
}
