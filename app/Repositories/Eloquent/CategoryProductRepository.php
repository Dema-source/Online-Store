<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Interfaces\CategoryProductRepositoryInterface;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryProductRepository implements CategoryProductRepositoryInterface
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
    public function getCategoryProducts(int|string $categoryId, array $filters = [], int $perPage = 15, array $relations = []): LengthAwarePaginator
    {
        $query = Category::findOrFail($categoryId)->products()->with($relations);

        // Apply filters using the Product model's filter scope
        $query->filter($filters);

        return $query->latest()->paginate($perPage);
    }

    /**
     * Get categories for a product with optional filters and relations.
     *
     * @param int|string $productId
     * @param array $filters
     * @param int $perPage
     * @param array $relations
     * @return LengthAwarePaginator
     */
    public function getProductCategories(int|string $productId, array $filters = [], int $perPage = 15, array $relations = []): LengthAwarePaginator
    {
        $query = Product::findOrFail($productId)->categories()->with($relations);

        // Apply filters using the Category model's filter scope
        $query->filter($filters);

        return $query->latest()->paginate($perPage);
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
        return Category::findOrFail($categoryId)
            ->products()
            ->where('product_id', $productId)
            ->exists();
    }

    /**
     * Attach a product to a category.
     *
     * @param int|string $categoryId
     * @param int|string $productId
     * @return bool
     */
    public function attachProduct(int|string $categoryId, int|string $productId): bool
    {
        $category = Category::findOrFail($categoryId);
        
        // Check if already attached
        if ($category->products()->where('product_id', $productId)->exists()) {
            return false;
        }

        $category->products()->attach($productId);
        return true;
    }

    /**
     * Detach a product from a category.
     *
     * @param int|string $categoryId
     * @param int|string $productId
     * @return int
     */
    public function detachProduct(int|string $categoryId, int|string $productId): int
    {
        return Category::findOrFail($categoryId)
            ->products()
            ->detach($productId);
    }

    /**
     * Attach multiple products to a category.
     *
     * @param int|string $categoryId
     * @param array $productIds
     * @return array
     */
    public function attachProducts(int|string $categoryId, array $productIds): array
    {
        // Validate all product IDs exist
        $existingProducts = Product::whereIn('id', $productIds)->pluck('id')->toArray();
        $invalidIds = array_diff($productIds, $existingProducts);

        if (!empty($invalidIds)) {
            throw new \InvalidArgumentException('Invalid product IDs: ' . implode(', ', $invalidIds));
        }

        $category = Category::findOrFail($categoryId);
        $attached = [];
        
        foreach ($productIds as $productId) {
            if (!$category->products()->where('product_id', $productId)->exists()) {
                $category->products()->attach($productId);
                $attached[] = $productId;
            }
        }

        return [
            'attached' => $attached,
            'attached_count' => count($attached),
            'skipped' => $invalidIds,
            'skipped_count' => count($invalidIds)
        ];
    }

    /**
     * Detach multiple products from a category.
     *
     * @param int|string $categoryId
     * @param array $productIds
     * @return array
     */
    public function detachProducts(int|string $categoryId, array $productIds): array
    {
        // Validate all product IDs exist
        $existingProducts = Product::whereIn('id', $productIds)->pluck('id')->toArray();
        $invalidIds = array_diff($productIds, $existingProducts);

        if (!empty($invalidIds)) {
            throw new \InvalidArgumentException('Invalid product IDs: ' . implode(', ', $invalidIds));
        }

        $category = Category::findOrFail($categoryId);
        $detached = [];
        
        foreach ($productIds as $productId) {
            if ($category->products()->where('product_id', $productId)->exists()) {
                $category->products()->detach($productId);
                $detached[] = $productId;
            }
        }

        return [
            'detached' => $detached,
            'detached_count' => count($detached),
            'skipped' => $invalidIds,
            'skipped_count' => count($invalidIds)
        ];
    }

    /**
     * Sync products for a category (replaces all existing relationships).
     *
     * @param int|string $categoryId
     * @param array $productIds
     * @return array
     */
    public function syncProducts(int|string $categoryId, array $productIds): array
    {
        // Validate all product IDs exist
        $existingProducts = Product::whereIn('id', $productIds)->pluck('id')->toArray();
        $invalidIds = array_diff($productIds, $existingProducts);

        if (!empty($invalidIds)) {
            throw new \InvalidArgumentException('Invalid product IDs: ' . implode(', ', $invalidIds));
        }

        return Category::findOrFail($categoryId)->products()->sync($productIds);
    }

    /**
     * Get products count for categories.
     *
     * @param array $categoryIds
     * @return array
     */
    public function getProductsCount(array $categoryIds = []): array
    {
        $query = Category::withCount('products');

        if (!empty($categoryIds)) {
            $query->whereIn('id', $categoryIds);
        }

        return $query->get()->map(function ($category) {
            return [
                'category_id' => $category->id,
                'category_name' => $category->name,
                'products_count' => $category->products_count
            ];
        })->toArray();
    }

    /**
     * Get categories count for products.
     *
     * @param array $productIds
     * @return array
     */
    public function getCategoriesCount(array $productIds = []): array
    {
        $query = Product::withCount('categories');

        if (!empty($productIds)) {
            $query->whereIn('id', $productIds);
        }

        return $query->get()->map(function ($product) {
            return [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'categories_count' => $product->categories_count
            ];
        })->toArray();
    }

    /**
     * Get products by multiple category IDs.
     *
     * @param array $categoryIds
     * @param array $filters
     * @param int $perPage
     * @param array $relations
     * @return LengthAwarePaginator
     */
    public function getProductsByCategories(array $categoryIds, array $filters = [], int $perPage = 15, array $relations = []): LengthAwarePaginator
    {
        $query = Product::whereHas('categories', function ($q) use ($categoryIds) {
            $q->whereIn('categories.id', $categoryIds);
        })->with($relations);

        // Apply filters using the Product model's filter scope
        $query->filter($filters);

        return $query->latest()->paginate($perPage);
    }

    /**
     * Get categories by multiple product IDs.
     *
     * @param array $productIds
     * @param array $filters
     * @param int $perPage
     * @param array $relations
     * @return LengthAwarePaginator
     */
    public function getCategoriesByProducts(array $productIds, array $filters = [], int $perPage = 15, array $relations = []): LengthAwarePaginator
    {
        $query = Category::whereHas('products', function ($q) use ($productIds) {
            $q->whereIn('products.id', $productIds);
        })->with($relations);

        // Apply filters using the Category model's filter scope
        $query->filter($filters);

        return $query->latest()->paginate($perPage);
    }
}
