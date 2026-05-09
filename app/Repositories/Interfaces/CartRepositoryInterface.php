<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Cart;

/**
 * Interface CartRepositoryInterface
 *
 * Defines the contract for CRUD operations.
 */
interface CartRepositoryInterface
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
     * @return Cart 
     */
    public function findById(int|string $id): Cart;

    /**
     * Create a new record using the given data array.
     *
     * @param array $data.
     * @return Cart
     */
    public function create(array $data): Cart;

    /**
     * Update an existing record by ID with a given data.
     *
     * @param int|string $id The primary key value.
     * @param array $data.
     * @return Cart
     */
    public function update(int|string $id, array $data): Cart;

    /**
     * Delete a record by ID.
     *
     * @param int|string $id The primary key value.
     * @return bool
     */
    public function delete(int|string $id): bool;

    /**
     * Get carts with relationships loaded.
     *
     * @param array $relations Relations to load.
     * @param array $filters Optional filters.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getAllWithRelations(array $relations = [], array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Get cart by ID with relationships.
     *
     * @param int|string $id The cart ID.
     * @param array $relations Relations to load.
     * @return Cart
     */
    public function findByIdWithRelations(int|string $id, array $relations = []): Cart;

    /**
     * Get carts by multiple IDs.
     *
     * @param array $ids Array of cart IDs.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getByIds(array $ids, int $perPage = 15): LengthAwarePaginator;

    /**
     * Find a cart by profile ID.
     *
     * @param int $profileId The profile ID to search for.
     * @return Cart|null
     */
    public function findByProfileId(int $profileId): ?Cart;
}
