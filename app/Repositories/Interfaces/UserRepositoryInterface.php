<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\User;

/**
 * Interface UserRepositoryInterface
 *
 * Defines the contract for CRUD operations.
 */
interface UserRepositoryInterface
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
     * @return User 
     */
    public function findById(int|string $id): User;

    /**
     * Create a new record using the given data array.
     *
     * @param array $data.
     * @return User
     */
    public function create(array $data): User;

    /**
     * Update an existing record by ID with a given data.
     *
     * @param int|string $id The primary key value.
     * @param array $data.
     * @return User
     */
    public function update(int|string $id, array $data): User;

    /**
     * Delete a record by ID.
     *
     * @param int|string $id The primary key value.
     * @return bool
     */
    public function delete(int|string $id): bool;

    /**
     * Get users with relationships loaded.
     *
     * @param array $relations Relations to load.
     * @param array $filters Optional filters.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getAllWithRelations(array $relations = [], array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Get user by ID with relationships.
     *
     * @param int|string $id The user ID.
     * @param array $relations Relations to load.
     * @return User
     */
    public function findByIdWithRelations(int|string $id, array $relations = []): User;

    /**
     * Get users by multiple IDs.
     *
     * @param array $userIds Array of user IDs.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getByIds(array $userIds, int $perPage = 15): LengthAwarePaginator;

    /**
     * Find a user by email address.
     *
     * @param string $email The email address to search for.
     * @return User|null
     */
    public function findByEmail(string $email): ?User;

    /**
     * Find a user by name.
     *
     * @param string $name The name to search for.
     * @return User|null
     */
    public function findByName(string $name): ?User;

    /**
     * Check if a user exists by email.
     *
     * @param string $email The email address to check.
     * @return bool
     */
    public function existsByEmail(string $email): bool;

    /**
     * Get total count of records with optional filters.
     *
     * @param array $filters Key/value filters to apply to the query.
     * @return int
     */
    public function count(array $filters = []): int;

    /**
     * Get users created recently.
     *
     * @param int $days Number of days.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getRecentUsers(int $days = 30, int $perPage = 15): LengthAwarePaginator;
}
