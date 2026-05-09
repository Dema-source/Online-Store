<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Repositories\Interfaces\CategoryProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryProductService
{
    /**
     * CategoryProductService Constructor.
     *
     * @param CategoryProductRepositoryInterface $repository
     */
    public function __construct(
        protected CategoryProductRepositoryInterface $repository
    ) {}

    /**
     * Get all products for a specific category with optional filters.
     *
     * @param int|string $categoryId
     * @param array $filters
     * @param int $perPage
     * @param array $relations
     * @return LengthAwarePaginator
     */
    public function getCategoryProducts(int|string $categoryId, array $filters = [], int $perPage = 15, array $relations = []): LengthAwarePaginator
    {
        return $this->repository->getCategoryProducts($categoryId, $filters, $perPage, $relations);
    }

    /**
     * Get all categories for a specific product with optional filters.
     *
     * @param int|string $productId
     * @param array $filters
     * @param int $perPage
     * @param array $relations
     * @return LengthAwarePaginator
     */
    public function getProductCategories(int|string $productId, array $filters = [], int $perPage = 15, array $relations = []): LengthAwarePaginator
    {
        return $this->repository->getProductCategories($productId, $filters, $perPage, $relations);
    }

    /**
     * Attach product(s) to a category.
     * Supports both single product and multiple products.
     *
     * @param int|string $categoryId
     * @param array $data
     * @return array
     */
    public function attachProduct(int|string $categoryId, array $data): array
    {
        // Handle single product (backward compatibility)
        if (isset($data['product_id'])) {
            $attached = $this->repository->attachProduct($categoryId, $data['product_id']);
            
            return [
                'attached' => $attached,
                'message' => $attached ? 'Product attached successfully' : 'Product already attached to category',
                'category_id' => $categoryId,
                'product_id' => $data['product_id']
            ];
        }
        
        // Handle multiple products
        if (isset($data['product_ids']) && is_array($data['product_ids'])) {
            $attached = $this->repository->attachProducts($categoryId, $data['product_ids']);
            
            return [
                'attached' => $attached,
                'message' => 'Products attached successfully',
                'category_id' => $categoryId,
                'product_ids' => $data['product_ids'],
                'attached_count' => count($data['product_ids'])
            ];
        }
        
        return [
            'attached' => false,
            'message' => 'No product_id or product_ids provided'
        ];
    }

    /**
     * Detach product(s) from a category.
     * Supports both single product and multiple products.
     *
     * @param int|string $categoryId
     * @param array $data
     * @return array
     */
    public function detachProduct(int|string $categoryId, array $data): array
    {
        // Handle single product (backward compatibility)
        if (isset($data['product_id'])) {
            $detached = $this->repository->detachProduct($categoryId, $data['product_id']);
            
            return [
                'detached' => $detached > 0,
                'message' => $detached > 0 ? 'Product detached successfully' : 'Product was not attached to category',
                'category_id' => $categoryId,
                'product_id' => $data['product_id']
            ];
        }
        
        // Handle multiple products
        if (isset($data['product_ids']) && is_array($data['product_ids'])) {
            $detached = $this->repository->detachProducts($categoryId, $data['product_ids']);
            
            return [
                'detached' => $detached,
                'message' => 'Products detached successfully',
                'category_id' => $categoryId,
                'product_ids' => $data['product_ids'],
                'detached_count' => count($data['product_ids'])
            ];
        }
        
        return [
            'detached' => false,
            'message' => 'No product_id or product_ids provided'
        ];
    }

    /**
     * Sync products for a category (replaces all existing relationships).
     *
     * @param int|string $categoryId
     * @param array $data
     * @return array
     */
    public function syncProducts(int|string $categoryId, array $data): array
    {
        $syncResult = $this->repository->syncProducts($categoryId, $data['product_ids']);

        return [
            'attached' => $syncResult['attached'],
            'detached' => $syncResult['detached'],
            'updated' => $syncResult['updated'],
            'category_id' => $categoryId,
            'product_ids' => $data['product_ids']
        ];
    }

    /**
     * Check if a product is attached to a category.
     *
     * @param int|string $categoryId
     * @param int|string $productId
     * @return bool
     */
    public function isProductAttached(int|string $categoryId, int|string $productId): bool
    {
        return $this->repository->isProductAttached($categoryId, $productId);
    }

    /**
     * Get products count for each category.
     *
     * @param array $categoryIds
     * @return array
     */
    public function getProductsCount(array $categoryIds = []): array
    {
        return $this->repository->getProductsCount($categoryIds);
    }

    /**
     * Get categories count for each product.
     *
     * @param array $productIds
     * @return array
     */
    public function getCategoriesCount(array $productIds = []): array
    {
        return $this->repository->getCategoriesCount($productIds);
    }
}
