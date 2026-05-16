<?php

namespace App\Services;

use App\Models\Profile;
use App\Repositories\Interfaces\ProfileRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Service layer for handling business logic related to the "ProfileRepositoryInterface" repository.
 */
class ProfileService
{
    /**
     * ProfileService Constructor.
     *
     * @param \App\Repositories\Interfaces\ProfileRepositoryInterface $repository
     */
    public function __construct(
        protected ProfileRepositoryInterface $repository
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
     * Update current user's profile.
     *
     * @param array $data The profile data to update.
     * @return Profile
     */
    public function updateMyProfile(array $data): Profile
    {
        $user = auth()->user();
        
        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        // Find the profile by user_id first
        $profile = $this->repository->findByUserId($user->id);
        
        if (!$profile) {
            throw new \Exception('Profile not found for this user');
        }

        // Update the profile using the actual profile ID
        return $this->repository->update($profile->id, $data);
    }

    /**
     * Get current user's profile.
     *
     * @return Profile|null
     */
    public function getMyProfile(): ?Profile
    {
        $user = auth()->user();
        
        if (!$user) {
            return null;
        }

        return $this->repository->findByUserId($user->id);
    }

    /**
     * Delete a record by ID.
     *
     * @param int|string $id The primary key value.
     * @return bool
     */
    public function delete(int|string $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Get profiles with relationships loaded.
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
     * Get profile by ID with relationships.
     *
     * @param int|string $id The profile ID.
     * @param array $relations Relations to load.
     * @return Profile
     */
    public function findByIdWithRelations(int|string $id, array $relations = []): Profile
    {
        return $this->repository->findByIdWithRelations($id, $relations);
    }

    /**
     * Get profiles by multiple IDs.
     *
     * @param array $ids Array of profile IDs.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getByIds(array $ids, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getByIds($ids, $perPage);
    }

    /**
     * Find a profile by user ID.
     *
     * @param int $userId The user ID to search for.
     * @return Profile|null
     */
    public function findByUserId(int $userId): ?Profile
    {
        return $this->repository->findByUserId($userId);
    }

    /**
     * Find a profile by phone number.
     *
     * @param string $phone The phone number to search for.
     * @return Profile|null
     */
    public function findByPhone(string $phone): ?Profile
    {
        return $this->repository->findByPhone($phone);
    }

    /**
     * Find a profile by address.
     *
     * @param string $address The address to search for.
     * @return Profile|null
     */
    public function findByAddress(string $address): ?Profile
    {
        return $this->repository->findByAddress($address);
    }

    /**
     * Find a profile by date of birth.
     *
     * @param string $dateOfBirth The date of birth to search for.
     * @return Profile|null
     */
    public function findByDateOfBirth(string $dateOfBirth): ?Profile
    {
        return $this->repository->findByDateOfBirth($dateOfBirth);
    }

    /**
     * Get profiles statistics.
     *
     * @return array
     */
    public function getStatistics(): array
    {
        return $this->repository->getStatistics();
    }

}