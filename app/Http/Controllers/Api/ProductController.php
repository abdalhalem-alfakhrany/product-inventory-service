<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\{CreateProductRequest, UpdateProductRequest, UpdateProductStockRequest};
use App\Http\Resources\{ProductCollection, ProductResource};
use App\Http\Controllers\Controller;
use App\Service\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Str;

/**
 * @OA\Info(
 *     title="Product Inventory API",
 *     version="1.0.0",
 *     description="RESTful API for managing products and stock levels"
 * )
 *
 * @OA\Server(
 *     url="/",
 *     description="Local API Server"
 * )
 *
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     @OA\Property(property="id", type="string", format="uuid", example="019e5c29-916b-722a-a4ea-971bc10809e3"),
 *     @OA\Property(property="name", type="string", example="Camera"),
 *     @OA\Property(property="sku", type="string", example="PRD-14142"),
 *     @OA\Property(property="description", type="string", nullable=true, example="A product description"),
 *     @OA\Property(property="price", type="number", format="float", example=727.00),
 *     @OA\Property(property="stock_quantity", type="integer", example=50),
 *     @OA\Property(property="low_stock_threshold", type="integer", example=10),
 *     @OA\Property(property="status", type="string", enum={"active", "inactive", "discontinued"}, example="active"),
 *     @OA\Property(property="created_at", type="string", format="datetime", example="2024-01-01T00:00:00Z")
 * )
 *
 * @OA\Schema(
 *     schema="Pagination",
 *     type="object",
 *     @OA\Property(property="total", type="integer", example=20),
 *     @OA\Property(property="per_page", type="integer", example=15),
 *     @OA\Property(property="current_page", type="integer", example=1),
 *     @OA\Property(property="last_page", type="integer", example=2),
 *     @OA\Property(property="next", type="string", nullable=true, example="http://localhost:8080/api/products?page=2"),
 *     @OA\Property(property="prev", type="string", nullable=true, example=null)
 * )
 *
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(property="message", type="string", example="Product not found")
 * )
 *
 * @OA\Schema(
 *     schema="ValidationErrorResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(property="message", type="string", example="Validation failed"),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         @OA\Property(property="name", type="array", @OA\Items(type="string", example="The name field is required."))
 *     )
 * )
 */
class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="List all products",
     *     description="Returns a paginated list of all products",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Product")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="pagination", ref="#/components/schemas/Pagination")
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        return new ProductCollection($this->productService->all($request->get('page', 1)));
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Get a single product",
     *     description="Returns a single product by UUID",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product UUID",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid", example="019e5c29-916b-722a-a4ea-971bc10809e3")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Product")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid UUID format",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function show(string $id)
    {
        if ($error = $this->validateUuid($id))
            return $error;
        return (new ProductResource($this->productService->getProduct($id)))->response();
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Create a new product",
     *     description="Creates a new product and returns it",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "sku", "price", "status"},
     *             @OA\Property(property="name", type="string", example="Camera"),
     *             @OA\Property(property="sku", type="string", example="PRD-14142"),
     *             @OA\Property(property="description", type="string", nullable=true, example="A great camera"),
     *             @OA\Property(property="price", type="number", format="float", example=727.00),
     *             @OA\Property(property="stock_quantity", type="integer", example=50),
     *             @OA\Property(property="low_stock_threshold", type="integer", example=10),
     *             @OA\Property(property="status", type="string", enum={"active", "inactive", "discontinued"}, example="active")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Product")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     )
     * )
     */
    public function store(CreateProductRequest $request)
    {
        return (new ProductResource($this->productService->createProduct($request->validated())))->response();
    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary="Update a product",
     *     description="Updates an existing product by UUID",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product UUID",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid", example="019e5c29-916b-722a-a4ea-971bc10809e3")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Updated Camera"),
     *             @OA\Property(property="description", type="string", nullable=true, example="Updated description"),
     *             @OA\Property(property="price", type="number", format="float", example=599.00),
     *             @OA\Property(property="low_stock_threshold", type="integer", example=5),
     *             @OA\Property(property="status", type="string", enum={"active", "inactive", "discontinued"}, example="active")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid UUID or validation failed",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/products/{id}/stock",
     *     summary="Adjust product stock",
     *     description="Increment or decrement stock quantity for a product",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product UUID",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid", example="019e5c29-916b-722a-a4ea-971bc10809e3")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"adjustment"},
     *             @OA\Property(property="adjustment", type="integer", example=-10, description="Positive to increment, negative to decrement")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Stock adjusted",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid UUID or stock would go below zero",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/products/low-stock",
     *     summary="List low stock products",
     *     description="Returns all products where stock quantity is below the low stock threshold",
     *     tags={"Products"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Product")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="pagination", ref="#/components/schemas/Pagination")
     *             )
     *         )
     *     )
     * )
     */
    public function low_stock()
    {
        return new ProductCollection($this->productService->lowStock());
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Delete a product",
     *     description="Soft deletes a product by UUID",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product UUID",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid", example="019e5c29-916b-722a-a4ea-971bc10809e3")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Product deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
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
