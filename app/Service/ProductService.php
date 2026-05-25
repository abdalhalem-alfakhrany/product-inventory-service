<?php

namespace App\Service;

use App\Repositories\ProductRepositoryInterface;
use Cache;
use Log;
use Str;

class ProductService
{
    public function __construct(
        private ProductRepositoryInterface $repository
    ) {
    }

    public function all($page, $perPage = 15)
    {
        return Cache::tags(['products'])->remember("products:page:$page", now()->addHour(), function () use ($perPage, $page) {
            Log::info('get data from database ' . $page);
            return $this->repository->all($page, $perPage);
        });
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
        $this->clearCache();
        $data['sku'] = Str::replace(' ', '_', Str::upper($data['name'])) . '-' . Str::random(8);
        return $this->repository->create($data);
    }
    public function updateProduct(string $id, array $data)
    {
        $this->clearCache();
        return $this->repository->update($id, $data);
    }

    public function updateProductStock(string $id, array $data)
    {
        $this->clearCache();
        return $this->repository->update($id, $data);
    }

    public function deleteProduct(string $id)
    {
        $this->clearCache();
        return $this->repository->delete($id);
    }

    private function clearCache(): void
    {
        Cache::tags(['products'])->flush();
    }
}
