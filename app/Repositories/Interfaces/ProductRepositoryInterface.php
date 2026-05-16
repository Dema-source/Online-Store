<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Product;

/**
 * Interface ProductRepositoryInterface
 *
 * Defines the contract for CRUD operations.
 */
interface ProductRepositoryInterface
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
     * @return Product 
     */
    public function findById(int|string $id): Product;

    /**
     * Create a new record using the given data array.
     *
     * @param array $data.
     * @return Product
     */
    public function create(array $data): Product;

    /**
     * Update an existing record by ID with a given data.
     *
     * @param int|string $id The primary key value.
     * @param array $data.
     * @return Product
     */
    public function update(int|string $id, array $data): Product;

    /**
     * Delete a record by ID.
     *
     * @param int|string $id The primary key value.
     * @return bool
     */
    public function delete(int|string $id): bool;

        /**
     * Get products with relationships loaded.
     *
     * @param array $relations Relations to load.
     * @param array $filters Optional filters.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getAllWithRelations(array $relations = [], array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Get product by ID with relationships.
     *
     * @param int|string $id The product ID.
     * @param array $relations Relations to load.
     * @return Product
     */
    public function findByIdWithRelations(int|string $id, array $relations = []): Product;

    /**
     * Get products by multiple IDs.
     *
     * @param array $ids Array of product IDs.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getByIds(array $ids, int $perPage = 15): LengthAwarePaginator;

}