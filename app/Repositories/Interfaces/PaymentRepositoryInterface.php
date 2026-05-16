<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Payment;

/**
 * Interface PaymentRepositoryInterface
 *
 * Defines the contract for CRUD operations.
 */
interface PaymentRepositoryInterface
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
     * @return Payment 
     */
    public function findById(int|string $id): Payment;

    /**
     * Delete a record by ID.
     *
     * @param int|string $id The primary key value.
     * @return bool
     */
    public function delete(int|string $id): bool;

    /**
     * Get payment details by order ID.
     *
     * @param int $orderId
     * @return Payment|null
     */
    public function getPaymentByOrderId(int $orderId): ?Payment;
    
    /**
     * Update payment status.
     *
     * @param int $paymentId
     * @param string $status
     * @return bool
     */
    public function updatePaymentStatus(int $paymentId, string $status): bool;

    /**
     * Get payment status.
     *
     * @param int $paymentId
     * @return string|null
     */
    public function getPaymentStatus(int $paymentId): ?string;
}