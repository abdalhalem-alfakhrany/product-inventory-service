<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Service\ProductService;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {
    }
    public function index()
    {
        return new ProductCollection($this->productService->all());
    }

    public function show(string $id)
    {
        return (new ProductResource($this->productService->getProduct($id)))->response();
    }

    public function store(CreateProductRequest $request)
    {
        return (new ProductResource($this->productService->createProduct($request->validated())))->response();
    }

    public function update(string $id, UpdateProductRequest $request)
    {
        $success = $this->productService->updateProduct($id, $request->validated());
        return Response([
            'success' => $success,
            'data' => [],
            'meta' => []
        ]);
    }
}
