<?php

namespace App\Repositories\Eloquent;

use App\Models\Profile;
use App\Repositories\Interfaces\ProfileRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProfileRepository implements ProfileRepositoryInterface
{
    /**  
     * Dependency injection of the Eloquent model.  
     *  
     * @param Profile $model  
     */
    public function __construct(
        protected Profile $model
    ) {}

    /**  
     * Get a paginated list of records applying optional filters using Profile model scopes.  
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

                    case 'user_id':
                        // Handle user ID filter
                        $query->byUserId($value);
                        break;

                    case 'phone':
                        // Handle phone filter
                        $query->byPhone($value);
                        break;

                    case 'address':
                        // Handle address filter
                        $query->byAddress($value);
                        break;

                    case 'date_of_birth':
                        // Handle date of birth filter
                        $query->byDateOfBirth($value);
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
     * @return Profile  
     */
    public function findById(int|string $id): Profile
    {
        return $this->model->findOrFail($id);
    }

    /**  
     * Create a new record in the database.  
     *  
     * @param array $data Mass-Assignment Attributes for creating the model.
     * @return Profile  
     */
    public function create(array $data): Profile
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing record by ID with a given data.
     *
     * @param int|string $id The primary key value.
     * @param array $data.
     * @return Profile
     */
    public function update(int|string $id, array $data): Profile
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

        return (bool) $item->delete();
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
        $query = $this->model->with($relations);
        foreach ($filters as $field => $value) {
            if ($value !== null && $value !== '') {
                switch ($field) {
                    case 'search':
                        // Handle search term across multiple fields
                        $query->search($value);
                        break;

                    case 'user_id':
                        // Handle user ID filter
                        $query->byUserId($value);
                        break;

                    case 'phone':
                        // Handle phone filter
                        $query->byPhone($value);
                        break;

                    case 'address':
                        // Handle address filter
                        $query->byAddress($value);
                        break;

                    case 'date_of_birth':
                        // Handle date of birth filter
                        $query->byDateOfBirth($value);
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
     * Get profile by ID with relationships.
     *
     * @param int|string $id The profile ID.
     * @param array $relations Relations to load.
     * @return Profile
     */
    public function findByIdWithRelations(int|string $id, array $relations = []): Profile
    {
        return $this->model->with($relations)->findOrFail($id);
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
        return $this->model->byIds($ids)->latest()->paginate($perPage);
    }

    /**
     * Find a profile by user ID.
     *
     * @param int $userId The user ID to search for.
     * @return Profile|null
     */
    public function findByUserId(int $userId): ?Profile
    {
        return $this->model->byUserId($userId)->first();
    }

    /**
     * Find a profile by phone number.
     *
     * @param string $phone The phone number to search for.
     * @return Profile|null
     */
    public function findByPhone(string $phone): ?Profile
    {
        return $this->model->byPhone($phone)->first();
    }

    /**
     * Find a profile by address.
     *
     * @param string $address The address to search for.
     * @return Profile|null
     */
    public function findByAddress(string $address): ?Profile
    {
        return $this->model->byAddress($address)->first();
    }

    /**
     * Find a profile by date of birth.
     *
     * @param string $dateOfBirth The date of birth to search for.
     * @return Profile|null
     */
    public function findByDateOfBirth(string $dateOfBirth): ?Profile
    {
        return $this->model->byDateOfBirth($dateOfBirth)->first();
    }

    /**
     * Get profiles statistics.
     *
     * @return array
     */
    public function getStatistics(): array
    {
        return [
            'total_profiles' => $this->model->count(),
            'profiles_this_month' => $this->model->whereMonth('created_at', now()->month)->count(),
            'profiles_this_year' => $this->model->whereYear('created_at', now()->year)->count(),
        ];
    }
}
