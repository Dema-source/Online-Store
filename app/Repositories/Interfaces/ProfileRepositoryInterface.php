<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Profile;

/**
 * Interface ProfileRepositoryInterface
 *
 * Defines the contract for CRUD operations.
 */
interface ProfileRepositoryInterface
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
     * @return Profile 
     */
    public function findById(int|string $id): Profile;

    /**
     * Create a new record using the given data array.
     *
     * @param array $data.
     * @return Profile
     */
    public function create(array $data): Profile;

    /**
     * Update an existing record by ID with a given data.
     *
     * @param int|string $id The primary key value.
     * @param array $data.
     * @return Profile
     */
    public function update(int|string $id, array $data): Profile;

    /**
     * Delete a record by ID.
     *
     * @param int|string $id The primary key value.
     * @return bool
     */
    public function delete(int|string $id): bool;
}