<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductRepository implements ProductRepositoryInterface
{
    /**  
     * Dependency injection of the Eloquent model.  
     *  
     * @param Product $model  
     */
    public function __construct(
        protected Product $model
    ) {}

    /**  
     * Get a paginated list of records applying optional filters using Product model scopes.  
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
                        $query->search($value);
                        break;
                    case 'name':
                        $query->byName($value);
                        break;
                    case 'description':
                        $query->byDescription($value);
                        break;
                    case 'brand':
                        $query->byBrand($value);
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
     * @return Product  
     */
    public function findById(int|string $id): Product
    {
        return $this->model->findOrFail($id);
    }

    /**  
     * Create a new record in the database.  
     *  
     * @param array $data Mass-Assignment Attributes for creating the model.
     * @return Product  
     */
    public function create(array $data): Product
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing record by ID with a given data.
     *
     * @param int|string $id The primary key value.
     * @param array $data.
     * @return Product
     */
    public function update(int|string $id, array $data): Product
    {
        $item = $this->findById($id);
        $item->update($data);

        return $item->fresh();
    }

    /**
     * Get products with relationships loaded.
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
                        $query->search($value);
                        break;
                    case 'name':
                        $query->byName($value);
                        break;
                    case 'description':
                        $query->byDescription($value);
                        break;
                    case 'brand':
                        $query->byBrand($value);
                        break;
                    case 'created_from':
                        $query->createdFrom($value);
                        break;
                    case 'created_to':
                        $query->createdTo($value);
                        break;
                    case 'created_on':
                        $query->createdOn($value);
                        break;
                    default:
                        $query->where($field, $value);
                        break;
                }
            }
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Get product by ID with relationships.
     *
     * @param int|string $id The product ID.
     * @param array $relations Relations to load.
     * @return Product
     */
    public function findByIdWithRelations(int|string $id, array $relations = []): Product
    {
        return $this->model->with($relations)->findOrFail($id);
    }

    /**
     * Get products by multiple IDs.
     *
     * @param array $ids Array of product IDs.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getByIds(array $ids, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->byIds($ids)->latest()->paginate($perPage);
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
