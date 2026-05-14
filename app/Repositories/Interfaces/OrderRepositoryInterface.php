<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Order;

/**
 * Interface OrderRepositoryInterface
 *
 * Defines the contract for CRUD operations.
 */
interface OrderRepositoryInterface
{
    /**
     * Retrieve a paginated list of records with optional provided conditions.
     *
     * @param array $filters [Key => value] filters.
     * @param int $perPage size of items in each page.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Find a record by its ID.
     *
     * @param int|string $id The primary key value.
     * @return Order 
     */
    public function findById(int|string $id): Order;

    /**
     * Update an existing record by ID with a given data.
     *
     * @param int|string $id The primary key value.
     * @param array $data.
     * @return Order
     */
    public function update(int|string $id, array $data): Order;

    /**
     * Delete a record by ID.
     *
     * @param int|string $id The primary key value.
     * @return bool
     */
    public function delete(int|string $id): bool;

    /**
     * Update order status.
     *
     * @param int $orderId
     * @param string $status
     * @return bool
     */
    public function updateOrderStatus(int $orderId, string $status): bool;

    /**
     * Get order with its items and payment.
     *
     * @param int $orderId
     * @return array
     */
    public function getOrderWithDetails(int $orderId): array;

    /**
     * Get order status.
     *
     * @param int $orderId
     * @return string|null
     */
    public function getOrderStatus(int $orderId): ?string;

    /**
     * Get orders by multiple IDs.
     *
     * @param array $ids Array of order IDs.
     * @param int $perPage Number of items per page.
     * @return LengthAwarePaginator
     */
    public function getByIds(array $ids, int $perPage = 15): LengthAwarePaginator;

    /**
     * Check if order exists.
     *
     * @param int $orderId
     * @return bool
     */
    public function orderExists(int $orderId): bool;

    /**
     * Cancel an order by updating order and payment status.
     *
     * @param int $orderId
     * @return bool
     */
    public function cancelOrder(int $orderId): bool;
}
