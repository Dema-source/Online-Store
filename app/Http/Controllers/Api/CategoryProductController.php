<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CategoryProduct\AttachProductRequest;
use App\Http\Requests\Api\CategoryProduct\DetachProductRequest;
use App\Http\Requests\Api\CategoryProduct\SyncProductsRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryProductController extends Controller
{
    /**
     * CategoryProductController Constructor.
     *
     * @param CategoryProductService $service
     */
    public function __construct(
        protected CategoryProductService $service
    ) {}

    /**
     * Get all products for a specific category.
     *
     * @param int|string $categoryId
     * @param Request $request
     * @return JsonResponse
     */
    public function index(int|string $categoryId, Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getCategoryProducts($categoryId, $filters, $perPage);

        return $this->paginate(ProductResource::collection($data), 'Category products fetched successfully');
    }

    /**
     * Get all categories for a specific product.
     *
     * @param int|string $productId
     * @param Request $request
     * @return JsonResponse
     */
    public function productCategories(int|string $productId, Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getProductCategories($productId, $filters, $perPage);

        return $this->paginate(CategoryResource::collection($data), 'Product categories fetched successfully');
    }

    /**
     * Attach a product to a category.
     *
     * @param int|string $categoryId
     * @param AttachProductRequest $request
     * @return JsonResponse
     */
    public function attach(int|string $categoryId, AttachProductRequest $request): JsonResponse
    {
        $result = $this->service->attachProduct($categoryId, $request->validated());

        return $this->success($result, 'Product attached to category successfully');
    }

    /**
     * Detach a product from a category.
     *
     * @param int|string $categoryId
     * @param DetachProductRequest $request
     * @return JsonResponse
     */
    public function detach(int|string $categoryId, DetachProductRequest $request): JsonResponse
    {
        $result = $this->service->detachProduct($categoryId, $request->validated());

        return $this->success($result, 'Product detached from category successfully');
    }

    /**
     * Sync products for a category (replaces all existing relationships).
     *
     * @param int|string $categoryId
     * @param SyncProductsRequest $request
     * @return JsonResponse
     */
    public function sync(int|string $categoryId, SyncProductsRequest $request): JsonResponse
    {
        $result = $this->service->syncProducts($categoryId, $request->validated());

        return $this->success($result, 'Category products synced successfully');
    }

    /**
     * Check if a product is attached to a category.
     *
     * @param int|string $categoryId
     * @param int|string $productId
     * @return JsonResponse
     */
    public function check(int|string $categoryId, int|string $productId): JsonResponse
    {
        $isAttached = $this->service->isProductAttached($categoryId, $productId);

        return $this->success([
            'attached' => $isAttached,
            'category_id' => $categoryId,
            'product_id' => $productId
        ], 'Product attachment status checked');
    }

}
