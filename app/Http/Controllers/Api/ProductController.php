<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\{CreateProductRequest, UpdateProductRequest, UpdateProductStockRequest};
use App\Http\Resources\{ProductCollection, ProductResource};
use App\Http\Controllers\Controller;
use App\Service\ProductService;
use Illuminate\Http\Response;
use Str;

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
        if ($error = $this->validateUuid($id))
            return $error;
        return (new ProductResource($this->productService->getProduct($id)))->response();
    }

    public function store(CreateProductRequest $request)
    {
        return (new ProductResource($this->productService->createProduct($request->validated())))->response();
    }

    public function update(string $id, UpdateProductRequest $request)
    {
        if ($error = $this->validateUuid($id))
            return $error;
        $success = $this->productService->updateProduct($id, $request->validated());
        return Response([
            'success' => $success,
            'data' => [],
            'meta' => []
        ]);
    }

    public function adjust_stock(string $id, UpdateProductStockRequest $request)
    {
        if ($error = $this->validateUuid($id))
            return $error;
        $success = $this->productService->updateProductStock($id, $request->validated());
        return new Response([
            'success' => $success,
            'data' => [],
            'meta' => []
        ]);
    }
    public function low_stock()
    {
        return new ProductCollection($this->productService->lowStock());
    }

    public function destroy(string $id)
    {
        $success = $this->productService->deleteProduct($id);
        return new Response([], 204);
    }

    private function validateUuid(string $id): ?Response
    {
        if (!Str::isUuid($id)) {
            return new Response([
                'success' => false,
                'message' => 'Invalid ID format, must be a valid UUID.',
            ], 422);
        }

        return null;
    }
}
