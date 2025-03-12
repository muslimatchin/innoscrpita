<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\PaginatedApiResponse;
use App\Http\Resources\ProductResource;
use App\Services\Interfaces\ProductServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    //

    protected ProductServiceInterface $productService;

    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }


    public function getAllProducts(Request $request): PaginatedApiResponse
    {
        $perPage = $request->input('perPage') ?? 15;
        $products = $this->productService->getAllProductsPaginated($perPage);
        return new PaginatedApiResponse([
            'message' => 'Products retrieved successfully.',
            'data' => ProductResource::collection($products),
            'pagination'  => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ]
        ]);
    }


    public function store(ProductRequest $request): ApiResponse
    {
        $validated = $request->validated();
        // Extract product images from the validated data
        $imagePaths = $validated['product_images'] ?? [];

        // Remove 'product_images' from the validated data before creating the product
        unset($validated['product_images']);

        // Extract category IDs from the validated data
        $categoryIds = $validated['category_ids'] ?? [];

        // Remove 'category_ids' from the validated data before creating the product
        unset($validated['category_ids']);

        $product = $this->productService->createProduct($validated);

        $product->categories()->sync($categoryIds);

        $this->productService->addImagesToProduct($product, $imagePaths);
        return new ApiResponse([
            'message' => 'Product created successfully.',
            'data' => new ProductResource($product),
        ]);
    }

    public function update(ProductRequest $request, int $id): ApiResponse
    {
        $validated = $request->validated();
        $updated = $this->productService->updateProduct($id, $validated);
        if(!$updated)
        {
            return response()->json(['message' => 'Product not found'], 400);
        }
        return new ApiResponse([
            'message' => 'Product updated successfully.',
            'data' => new ProductResource($updated),
        ]);

    }

    public function delete(int $id): JsonResponse
    {
        $deleted = $this->productService->deleteProduct($id);
        if(!$deleted)
        {
            return response()->json([
                'message' => 'Product not found.',
                'data' => null,
            ], 404);
        }
        return response()->json([
            'message' => 'Product deleted successfully.',
            'data' => null,
        ], 200);
        return $deleted
            ? response()->json(['message' => 'Product deleted successfully'])
            : response()->json(['message' => 'Product not found'], 404);
    }


    public function productDetails(int $id): ApiResponse
    {
        $product = $this->productService->getProductById($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 400);
        }
        return new ApiResponse([
            'message' => 'Product retrieved successfully.',
            'data' => new ProductResource($product),
        ]);
    }


    public function getAllProductsPublic(Request $request): PaginatedApiResponse | JsonResponse
    {
        $perPage = $request->input('perPage') ?? 15;
        // Retrieve category_ids from query parameters
        $categoryIds = $request->input('category_ids', '');

        // Validate that category_ids is a string of integers separated by commas
        if (!preg_match('/^(\d+)(,\d+)*$/', $categoryIds)) {
            return response()->json(['error' => 'Invalid category_ids parameter.'], 400);
        }

        // Convert the comma-separated string to an array of integers
        $categoryIdsArray = array_map('intval', explode(',', $categoryIds));
        $filters = [
            'category_ids' => $categoryIdsArray,
            'published' => true
        ];


        $products = $this->productService->getAllProductsPaginated($perPage,$filters);

        return new PaginatedApiResponse([
            'message' => 'Products retrieved successfully.',
            'data' => ProductResource::collection($products),
            'pagination'  => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ]
        ]);
    }


}
