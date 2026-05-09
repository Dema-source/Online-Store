<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Category;
use App\Models\Product;

/**
 * Interface CategoryProductRepositoryInterface
 *
 * Defines the contract for Category-Product relationship operations.
 */
interface CategoryProductRepositoryInterface
{
    /**
     * Get products for a category with optional filters and relations.
     *
     * @param int|string $categoryId
     * @param array $filters
     * @param int $perPage
     * @param array $relations
     * @return LengthAwarePaginator
     */
    public function getCategoryProducts(int|string $categoryId, array $filters = [], int $perPage = 15, array $relations = []): LengthAwarePaginator;

    /**
     * Get categories for a product with optional filters and relations.
     *
     * @param int|string $productId
     * @param array $filters
     * @param int $perPage
     * @param array $relations
     * @return LengthAwarePaginator
     */
    public function getProductCategories(int|string $productId, array $filters = [], int $perPage = 15, array $relations = []): LengthAwarePaginator;

    /**
     * Check if a product is attached to a category.
     *
     * @param int|string $categoryId
     * @param int|string $productId
     * @return bool
     */
    public function isProductAttached(int|string $categoryId, int|string $productId): bool;

    /**
     * Attach a product to a category.
     *
     * @param int|string $categoryId
     * @param int|string $productId
     * @return bool
     */
    public function attachProduct(int|string $categoryId, int|string $productId): bool;

    /**
     * Attach multiple products to a category.
     *
     * @param int|string $categoryId
     * @param array $productIds
     * @return array
     */
    public function attachProducts(int|string $categoryId, array $productIds): array;

    /**
     * Detach a product from a category.
     *
     * @param int|string $categoryId
     * @param int|string $productId
     * @return int
     */
    public function detachProduct(int|string $categoryId, int|string $productId): int;

    /**
     * Detach multiple products from a category.
     *
     * @param int|string $categoryId
     * @param array $productIds
     * @return array
     */
    public function detachProducts(int|string $categoryId, array $productIds): array;

    /**
     * Sync products for a category (replaces all existing relationships).
     *
     * @param int|string $categoryId
     * @param array $productIds
     * @return array
     */
    public function syncProducts(int|string $categoryId, array $productIds): array;

    /**
     * Get products count for categories.
     *
     * @param array $categoryIds
     * @return array
     */
    public function getProductsCount(array $categoryIds = []): array;

    /**
     * Get categories count for products.
     *
     * @param array $productIds
     * @return array
     */
    public function getCategoriesCount(array $productIds = []): array;

    /**
     * Get products by multiple category IDs.
     *
     * @param array $categoryIds
     * @param array $filters
     * @param int $perPage
     * @param array $relations
     * @return LengthAwarePaginator
     */
    public function getProductsByCategories(array $categoryIds, array $filters = [], int $perPage = 15, array $relations = []): LengthAwarePaginator;

    /**
     * Get categories by multiple product IDs.
     *
     * @param array $productIds
     * @param array $filters
     * @param int $perPage
     * @param array $relations
     * @return LengthAwarePaginator
     */
    public function getCategoriesByProducts(array $productIds, array $filters = [], int $perPage = 15, array $relations = []): LengthAwarePaginator;
}
