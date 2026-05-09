<?php

namespace App\Repositories\Eloquent;

use App\Models\ProductImage;
use App\Repositories\Interfaces\ProductImageRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductImageRepository implements ProductImageRepositoryInterface
{
    /**  
     * Dependency injection of the Eloquent model.  
     *  
     * @param ProductImage $model  
     */ 
    public function __construct(
        protected ProductImage $model
    ) {}

    /**  
     * Get a paginated list of records applying optional filters using ProductImage model scopes.  
     *  
     * @param array $filters Key/value filters to apply to the query.  
     * @param int $perPage Number of items per page.  
     * @return LengthAwarePaginator  
     */  
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->query();

        // Apply filters using the ProductImage model's filter scope
        $query->filter($filters);

        return $query->latest()->paginate($perPage);
    }

    /**  
     * Retrieve a single record by ID or throw an exception if not found.  
     *  
     * @param int|string $id  
     * @return ProductImage  
     */ 
    public function findById(int|string $id): ProductImage
    {
        return $this->model->findOrFail($id);
    }

    /**  
     * Create a new record in the database.  
     *  
     * @param array $data Mass-Assignment Attributes for creating the model.
     * @return ProductImage  
     */
    public function create(array $data): ProductImage
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing record by ID with a given data.
     *
     * @param int|string $id The primary key value.
     * @param array $data.
     * @return ProductImage
     */
    public function update(int|string $id, array $data): ProductImage
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
     * Get product images with relationships loaded.
     *
     * @param array $relations Relations to load.
     * @param array $filters Optional filters.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getAllWithRelations(array $relations = [], array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with($relations);

        // Apply filters using the ProductImage model's filter scope
        $query->filter($filters);

        return $query->latest()->paginate($perPage);
    }

    /**
     * Get product image by ID with relationships.
     *
     * @param int|string $id The product image ID.
     * @param array $relations Relations to load.
     * @return ProductImage
     */
    public function findByIdWithRelations(int|string $id, array $relations = []): ProductImage
    {
        return $this->model->with($relations)->findOrFail($id);
    }

    /**
     * Get product images by multiple IDs.
     *
     * @param array $ids Array of product image IDs.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getByIds(array $ids, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->whereIn('id', $ids)->latest()->paginate($perPage);
    }
}
