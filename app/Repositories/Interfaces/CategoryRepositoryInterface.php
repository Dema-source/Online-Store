<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Category;

/**
 * Interface CategoryRepositoryInterface
 *
 * Defines the contract for CRUD operations.
 */
interface CategoryRepositoryInterface
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
     * @return Category 
     */
    public function findById(int|string $id): Category;

    /**
     * Create a new record using the given data array.
     *
     * @param array $data.
     * @return Category
     */
    public function create(array $data): Category;

    /**
     * Update an existing record by ID with a given data.
     *
     * @param int|string $id The primary key value.
     * @param array $data.
     * @return Category
     */
    public function update(int|string $id, array $data): Category;

    /**
     * Delete a record by ID.
     *
     * @param int|string $id The primary key value.
     * @return bool
     */
    public function delete(int|string $id): bool;

    /**
     * Get categories with relationships loaded.
     *
     * @param array $relations Relations to load.
     * @param array $filters Optional filters.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getAllWithRelations(array $relations = [], array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Get category by ID with relationships.
     *
     * @param int|string $id The category ID.
     * @param array $relations Relations to load.
     * @return Category
     */
    public function findByIdWithRelations(int|string $id, array $relations = []): Category;

    /**
     * Get categories by multiple IDs.
     *
     * @param array $ids Array of category IDs.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getByIds(array $ids, int $perPage = 15): LengthAwarePaginator;
}
