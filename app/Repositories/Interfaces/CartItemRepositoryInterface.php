<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\CartItem;

/**
 * Interface CartItemRepositoryInterface
 *
 * Defines the contract for CartItem CRUD operations.
 */
interface CartItemRepositoryInterface
{
    /**
     * Get all cart items with optional filters.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Find cart item by ID.
     *
     * @param int|string $id
     * @return CartItem
     */
    public function findById(int|string $id): CartItem;

    /**
     * Create cart item.
     *
     * @param array $data
     * @return CartItem
     */
    public function create(array $data): CartItem;

    /**
     * Update cart item.
     *
     * @param int|string $id
     * @param array $data
     * @return CartItem
     */
    public function update(int|string $id, array $data): CartItem;

    /**
     * Delete cart item.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete(int|string $id): bool;

    /**
     * Get cart items with relations.
     *
     * @param array $relations
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllWithRelations(array $relations = [], array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Find cart item by ID with relations.
     *
     * @param int|string $id
     * @param array $relations
     * @return CartItem
     */
    public function findByIdWithRelations(int|string $id, array $relations = []): CartItem;

    /**
     * Get cart items by multiple IDs.
     *
     * @param array $ids
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByIds(array $ids, int $perPage = 15): LengthAwarePaginator;

    /**
     * Find cart item by cart and product.
     *
     * @param int|string $cartId
     * @param int|string $productId
     * @return CartItem|null
     */
    public function findByCartAndProduct(int|string $cartId, int|string $productId): ?CartItem;

    /**
     * Update or create cart item.
     *
     * @param int|string $cartId
     * @param int|string $productId
     * @param int $quantity
     * @return CartItem
     */
    public function updateOrCreate(int|string $cartId, int|string $productId, int $quantity): CartItem;

    /**
     * Clear all items from a cart.
     *
     * @param int|string $cartId
     * @return int
     */
    public function clearCart(int|string $cartId): int;

    /**
     * Get cart total value.
     *
     * @param int|string $cartId
     * @return float
     */
    public function getCartTotal(int|string $cartId): float;

    /**
     * Get cart items count.
     *
     * @param int|string $cartId
     * @return int
     */
    public function getCartItemsCount(int|string $cartId): int;

    /**
     * Get product carts count.
     *
     * @param int|string $productId
     * @return int
     */
    public function getProductCartsCount(int|string $productId): int;
    
    /**
     * Check if products exist in cart.
     *
     * @param int|string $cartId
     * @param array $productIds
     * @return array
     */
    public function checkProductsInCart(int|string $cartId, array $productIds): array;
}