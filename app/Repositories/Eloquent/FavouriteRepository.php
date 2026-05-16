<?php

namespace App\Repositories\Eloquent;

use App\Models\Favourite;
use App\Repositories\Interfaces\FavouriteRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FavouriteRepository implements FavouriteRepositoryInterface
{
    /**  
     * Dependency injection of the Eloquent model.  
     *  
     * @param Favourite $model  
     */ 
    public function __construct(
        protected Favourite $model
    ) {}

    /**
     * Get a paginated list of records applying optional filters.
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
                        // Handle search term across product names
                        $query->search($value);
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

                    case 'created_this_month':
                        // Handle created this month filter
                        $query->createdThisMonth();
                        break;

                    case 'user_id':
                        // Handle user ID filter
                        $query->byUser($value);
                        break;

                    case 'product_id':
                        // Handle product ID filter
                        $query->byProduct($value);
                        break;

                    case 'guest_token':
                        // Handle guest token filter
                        $query->byGuestToken($value);
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
     * @return Favourite  
     */ 
    public function findById(int|string $id): Favourite
    {
        return $this->model->findOrFail($id);
    }

    /**  
     * Create a new record in the database.  
     *  
     * @param array $data Mass-Assignment Attributes for creating the model.
     * @return Favourite  
     */
    public function create(array $data): Favourite
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing record by ID with a given data.
     *
     * @param int|string $id The primary key value.
     * @param array $data.
     * @return Favourite
     */
    public function update(int|string $id, array $data): Favourite
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
}