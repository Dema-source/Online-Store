<?php

namespace App\Services;

use App\Repositories\Interfaces\CartItemRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Service layer for handling business logic related to the "CartItemRepositoryInterface" repository.
 */
class CartItemService
{
    /**
     * CartItemService Constructor.
     *
     * @param \App\Repositories\Interfaces\CartItemRepositoryInterface $repository
     */
    public function __construct(
        protected CartItemRepositoryInterface $repository
    ) {}

    /**
     * Retrieve a paginated list of records applying optional dynamic filters.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getAll($filters, $perPage);
    }

    /**
     * Find a record by its ID.
     *
     * @param int|string $id
     * @return mixed
     */
    public function findById(int|string $id): mixed
    {
        return $this->repository->findById($id);
    }

    /**
     * Create cart item.
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data): mixed
    {
        return $this->repository->create($data);
    }

    /**
     * Update cart item.
     *
     * @param int|string $id
     * @param array $data
     * @return mixed
     */
    public function update(int|string $id, array $data): mixed
    {
        return $this->repository->update($id, $data);
    }

    /**
     * Delete a record by ID.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete(int|string $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Get cart items with relations.
     *
     * @param array $relations
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllWithRelations(array $relations = [], array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getAllWithRelations($relations, $filters, $perPage);
    }

    /**
     * Find cart item by ID with relations.
     *
     * @param int|string $id
     * @param array $relations
     * @return mixed
     */
    public function findByIdWithRelations(int|string $id, array $relations = []): mixed
    {
        return $this->repository->findByIdWithRelations($id, $relations);
    }

    /**
     * Get cart items by multiple IDs.
     *
     * @param array $ids
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByIds(array $ids, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getByIds($ids, $perPage);
    }

    /**
     * Get cart items by cart ID.
     *
     * @param int|string $cartId
     * @param array $filters
     * @param int $perPage
     * @param array $relations
     * @return LengthAwarePaginator
     */
    public function getByCartId(int|string $cartId, array $filters = [], int $perPage = 15, array $relations = []): LengthAwarePaginator
    {
        // Use getAll method with cart_id filter instead
        $filters['cart_id'] = $cartId;
        return $this->repository->getAll($filters, $perPage);
    }

    /**
     * Get cart items by product ID.
     *
     * @param int|string $productId
     * @param array $filters
     * @param int $perPage
     * @param array $relations
     * @return LengthAwarePaginator
     */
    public function getByProductId(int|string $productId, array $filters = [], int $perPage = 15, array $relations = []): LengthAwarePaginator
    {
        // Use getAll method with product_id filter instead
        $filters['product_id'] = $productId;
        return $this->repository->getAll($filters, $perPage);
    }

    /**
     * Find cart item by cart and product.
     *
     * @param int|string $cartId
     * @param int|string $productId
     * @return mixed
     */
    public function findByCartAndProduct(int|string $cartId, int|string $productId): mixed
    {
        return $this->repository->findByCartAndProduct($cartId, $productId);
    }

    /**
     * Update or create cart item.
     *
     * @param int|string $cartId
     * @param int|string $productId
     * @param int $quantity
     * @return mixed
     */
    public function updateOrCreate(int|string $cartId, int|string $productId, int $quantity): mixed
    {
        return $this->repository->updateOrCreate($cartId, $productId, $quantity);
    }

    /**
     * Clear all items from a cart.
     *
     * @param int|string $cartId
     * @return int
     */
    public function clearCart(int|string $cartId): int
    {
        return $this->repository->clearCart($cartId);
    }

    /**
     * Get cart total value.
     *
     * @param int|string $cartId
     * @return float
     */
    public function getCartTotal(int|string $cartId): float
    {
        return $this->repository->getCartTotal($cartId);
    }

    /**
     * Get cart items count.
     *
     * @param int|string $cartId
     * @return int
     */
    public function getCartItemsCount(int|string $cartId): int
    {
        return $this->repository->getCartItemsCount($cartId);
    }

    /**
     * Get product carts count.
     *
     * @param int|string $productId
     * @return int
     */
    public function getProductCartsCount(int|string $productId): int
    {
        return $this->repository->getProductCartsCount($productId);
    }

    /**
     * Check if products are in cart.
     *
     * @param int|string $cartId
     * @param array $productIds
     * @return array
     */
    public function checkProductsInCart(int|string $cartId, array $productIds): array
    {
        return $this->repository->checkProductsInCart($cartId, $productIds);
    }
    }