<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    /**  
     * Dependency injection of the Eloquent model.  
     *  
     * @param User $model  
     */
    public function __construct(
        protected User $model
    ) {}

    /**  
     * Get a paginated list of records applying optional filters using User model scopes.  
     *  
     * @param array $filters Key/value filters to apply to the query.  
     * @param int $perPage Number of items per page.  
     * @return LengthAwarePaginator  
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->query();

        foreach ($filters as $field => $value) {
            if ($value !== null && $value !== '') {
                switch ($field) {
                    case 'search':
                        // Handle search term across multiple fields
                        $query->search($value);
                        break;

                    case 'name':
                        // Handle name filter
                        $query->byName($value);
                        break;

                    case 'email':
                        // Handle email filter
                        $query->byEmail($value);
                        break;

                    case 'created_on':
                        // Handle created on date filter
                        $query->createdOn($value);
                        break;

                    case 'created_from':
                        // Handle created from date filter
                        $query->createdFrom($value);
                        break;

                    case 'created_to':
                        // Handle created to date filter
                        $query->createdTo($value);
                        break;

                    default:
                        // Handle individual filter scopes
                        $query->where($field, $value);
                        break;
                }
            }
        }

        return $query->latest()->paginate($perPage);
    }

    /**  
     * Retrieve a single record by ID or throw an exception if not found.  
     *  
     * @param int|string $id  
     * @return User  
     */
    public function findById(int|string $id): User
    {
        return $this->model->findOrFail($id);
    }

    /**  
     * Create a new record in the database.  
     *  
     * @param array $data Mass-Assignment Attributes for creating the model.
     * @return User  
     */
    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing record by ID with a given data.
     *
     * @param int|string $id The primary key value.
     * @param array $data.
     * @return User
     */
    public function update(int|string $id, array $data): User
    {
        $item = $this->findById($id);
        $item->update($data);

        return $item->fresh();
    }

    /**
     * Delete a record by ID.
     *
     * @param int|string $id The primary key value.
     * @return bool
     */
    public function delete(int|string $id): bool
    {
        $item = $this->findById($id);
        return $item->delete();
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
        $query = $this->model->with($relations);

        foreach ($filters as $field => $value) {
            if ($value !== null && $value !== '') {
                switch ($field) {
                    case 'search':
                        // Handle search term across multiple fields
                        $query->search($value);
                        break;

                    case 'name':
                        // Handle name filter
                        $query->byName($value);
                        break;

                    case 'email':
                        // Handle email filter
                        $query->byEmail($value);
                        break;

                    case 'created_on':
                        // Handle created on date filter
                        $query->createdOn($value);
                        break;

                    case 'created_from':
                        // Handle created from date filter
                        $query->createdFrom($value);
                        break;

                    case 'created_to':
                        // Handle created to date filter
                        $query->createdTo($value);
                        break;

                    default:
                        // Handle individual filter scopes
                        $query->where($field, $value);
                        break;
                }
            }
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Get user by ID with relationships.
     *
     * @param int|string $id The user ID.
     * @param array $relations Relations to load.
     * @return User
     */
    public function findByIdWithRelations(int|string $id, array $relations = []): User
    {
        return $this->model->with($relations)->find($id);
    }

    /**
     * Get users by multiple IDs.
     *
     * @param array $ids Array of user IDs.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getByIds(array $ids, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->byIds($ids)->latest()->paginate($perPage);
    }

    /**
     * Find a user by email address.
     *
     * @param string $email The email address to search for.
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return $this->model->ByEmail($email)->first();
    }

    /**
     * Find a user by name.
     *
     * @param string $name The name to search for.
     * @return User|null
     */
    public function findByName(string $name): ?User
    {
        return $this->model->ByName($name)->first();
    }

    /**
     * Check if a user exists by email.
     *
     * @param string $email The email address to check.
     * @return bool
     */
    public function existsByEmail(string $email): bool
    {
        return $this->model->ByEmail($email)->exists();
    }

    /**
     * Get total count of records with optional filters.
     *
     * @param array $filters Key/value filters to apply to the query.
     * @return int
     */
    public function count(array $filters = []): int
    {
        $query = $this->model->query();

        if (!empty($filters)) {
            $query->filter($filters);
        }

        return $query->count();
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
        return $this->model->where('created_at', '>=', now()->subDays($days))
            ->latest()
            ->paginate($perPage);
    }
}
