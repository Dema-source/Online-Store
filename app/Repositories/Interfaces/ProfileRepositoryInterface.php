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
     * Get profile by ID with relationships.
     *
     * @param int|string $id The profile ID.
     * @param array $relations Relations to load.
     * @return Profile
     */
    public function findByIdWithRelations(int|string $id, array $relations = []): Profile;

    /**
     * Get profiles by multiple IDs.
     *
     * @param array $profileIds Array of profile IDs.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getByIds(array $ids, int $perPage = 15): LengthAwarePaginator;

    /**
     * Find a profile by user ID.
     *
     * @param int $userId The user ID to search for.
     * @return Profile|null
     */
    public function findByUserId(int $userId): ?Profile;

    /**
     * Find a profile by phone number.
     *
     * @param string $phone The phone number to search for.
     * @return Profile|null
     */
    public function findByPhone(string $phone): ?Profile;

    /**
     * Find a profile by address.
     *
     * @param string $address The address to search for.
     * @return Profile|null
     */
    public function findByAddress(string $address): ?Profile;

    /**
     * Find a profile by date of birth.
     *
     * @param string $dateOfBirth The date of birth to search for.
     * @return Profile|null
     */
    public function findByDateOfBirth(string $dateOfBirth): ?Profile;
    
    /**
     * Get profiles statistics.
     *
     * @return array
     */
    public function getStatistics(): array;
}
