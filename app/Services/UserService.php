<?php

namespace App\Services;

use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Service layer for handling business logic related to the "UserRepositoryInterface" repository.
 */
class UserService
{
    /**
     * UserService Constructor.
     *
     * @param \App\Repositories\Interfaces\UserRepositoryInterface $repository
     */
    public function __construct(
        protected UserRepositoryInterface $repository
    ) {}

    /**
     * Retrieve a paginated list of records applying optional dynamic filters.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getAll($filters, $perPage);
    }

    /**
     * Find a record by its ID.
     *
     * @param int|string $id
     * @return mixed
     */
    public function findById(int|string $id): mixed
    {
        return $this->repository->findById($id);
    }

    /**
     * Create a new record using the provided data.
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data): mixed
    {
        return $this->repository->create($data);
    }

    /**
     * Update an existing record by ID with the given data.
     *
     * @param int|string $id
     * @param array $data
     * @return mixed
     */
    public function update(int|string $id, array $data): mixed
    {
        return $this->repository->update($id, $data);
    }

    /**
     * Delete a record by ID.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete(int|string $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Get users with relationships loaded.
     *
     * @param array $relations Relations to load.
     * @param array $filters Optional filters.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getAllWithRelations(array $relations = [], array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getAllWithRelations($relations, $filters, $perPage);
    }

    /**
     * Get user by ID with relationships.
     *
     * @param int|string $id The user ID.
     * @param array $relations Relations to load.
     * @return mixed
     */
    public function findByIdWithRelations(int|string $id, array $relations = []): mixed
    {
        return $this->repository->findByIdWithRelations($id, $relations);
    }

    /**
     * Get users by multiple IDs.
     *
     * @param array $userIds Array of user IDs.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getByIds(array $userIds, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getByIds($userIds, $perPage);
    }

    /**
     * Get users created recently.
     *
     * @param int $days Number of days.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getRecentUsers(int $days = 30, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getRecentUsers($days, $perPage);
    }

    /**
     * Find a user by email address.
     *
     * @param string $email The email address to search for.
     * @return mixed
     */
    public function findByEmail(string $email): mixed
    {
        return $this->repository->findByEmail($email);
    }

    /**
     * Find a user by name.
     *
     * @param string $name The name to search for.
     * @return mixed
     */
    public function findByName(string $name): mixed
    {
        return $this->repository->findByName($name);
    }

    /**
     * Check if a user exists by email.
     *
     * @param string $email The email address to check.
     * @return bool
     */
    public function existsByEmail(string $email): bool
    {
        return $this->repository->existsByEmail($email);
    }

    /**
     * Get total count of records with optional filters.
     *
     * @param array $filters Key/value filters to apply to the query.
     * @return int
     */
    public function count(array $filters = []): int
    {
        return $this->repository->count($filters);
    }
}