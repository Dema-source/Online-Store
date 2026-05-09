<?php

namespace App\Services;

use App\Repositories\Interfaces\CartRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Service layer for handling business logic related to the "CartRepositoryInterface" repository.
 */
class CartService
{
    /**
     * CartService Constructor.
     *
     * @param \App\Repositories\Interfaces\CartRepositoryInterface $repository
     */
    public function __construct(
        protected CartRepositoryInterface $repository
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
     * Get carts with relationships loaded.
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
     * Get cart by ID with relationships.
     *
     * @param int|string $id The cart ID.
     * @param array $relations Relations to load.
     * @return \App\Models\Cart
     */
    public function findByIdWithRelations(int|string $id, array $relations = []): \App\Models\Cart
    {
        return $this->repository->findByIdWithRelations($id, $relations);
    }

    /**
     * Get carts by multiple IDs.
     *
     * @param array $ids Array of cart IDs.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getByIds(array $ids, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getByIds($ids, $perPage);
    }

    /**
     * Find a cart by profile ID.
     *
     * @param int $profileId The profile ID to search for.
     * @return \App\Models\Cart|null
     */
    public function findByProfileId(int $profileId): ?\App\Models\Cart
    {
        return $this->repository->findByProfileId($profileId);
    }
}