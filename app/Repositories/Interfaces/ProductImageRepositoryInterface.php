<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\ProductImage;

/**
 * Interface ProductImageRepositoryInterface
 *
 * Defines the contract for CRUD operations.
 */
interface ProductImageRepositoryInterface
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
     * @return ProductImage 
     */
    public function findById(int|string $id): ProductImage;

    /**
     * Create a new record using the given data array.
     *
     * @param array $data.
     * @return ProductImage
     */
    public function create(array $data): ProductImage;

    /**
     * Update an existing record by ID with a given data.
     *
     * @param int|string $id The primary key value.
     * @param array $data.
     * @return ProductImage
     */
    public function update(int|string $id, array $data): ProductImage;

    /**
     * Delete a record by ID.
     *
     * @param int|string $id The primary key value.
     * @return bool
     */
    public function delete(int|string $id): bool;

    /**
     * Get product images with relationships loaded.
     *
     * @param array $relations Relations to load.
     * @param array $filters Optional filters.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getAllWithRelations(array $relations = [], array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Get product image by ID with relationships.
     *
     * @param int|string $id The product image ID.
     * @param array $relations Relations to load.
     * @return ProductImage
     */
    public function findByIdWithRelations(int|string $id, array $relations = []): ProductImage;

    /**
     * Get product images by multiple IDs.
     *
     * @param array $ids Array of product image IDs.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getByIds(array $ids, int $perPage = 15): LengthAwarePaginator;

}