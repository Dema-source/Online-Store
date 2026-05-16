<?php

namespace App\Services;

use App\Repositories\Interfaces\ProductImageRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Service layer for handling business logic related to the "ProductImage" repository.
 */
class ProductImageService
{
    /**
     * ProductImageService Constructor.
     *
     * @param \App\Repositories\Interfaces\ProductImageRepositoryInterface $repository
     */
    public function __construct(
        protected ProductImageRepositoryInterface $repository
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
     * @return \App\Models\ProductImage
     */
    public function findById(int|string $id): \App\Models\ProductImage
    {
        return $this->repository->findById($id);
    }

    /**
     * Create a new record using the provided data.
     *
     * @param array $data
     * @return \App\Models\ProductImage
     */
    public function create(array $data): \App\Models\ProductImage
    {
        // Handle file upload if present
        if (isset($data['file']) && $data['file'] instanceof UploadedFile) {
            $file = $data['file'];
            $data['path'] = $this->uploadFile($file);
            $data['type'] = $this->getFileType($file);
            unset($data['file']);
        }

        return $this->repository->create($data);
    }

    /**
     * Upload file to storage and return the path.
     *
     * @param UploadedFile $file
     * @return string
     */
    protected function uploadFile(UploadedFile $file): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('product-images', $filename, 'public');
        
        return 'storage/' . $path;
    }

    /**
     * Get file type from uploaded file.
     *
     * @param UploadedFile $file
     * @return string
     */
    protected function getFileType(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        
        // Map extensions to our allowed types
        return match($extension) {
            'jpg', 'jpeg' => 'jpg',
            'png' => 'png',
            default => 'jpg' // default fallback
        };
    }

    /**
     * Update an existing record by ID with the given data.
     *
     * @param int|string $id
     * @param array $data
     * @return \App\Models\ProductImage
     */
    public function update(int|string $id, array $data): \App\Models\ProductImage
    {
        // Handle file upload if present
        if (isset($data['file']) && $data['file'] instanceof UploadedFile) {
            $file = $data['file'];
            $data['path'] = $this->uploadFile($file);
            $data['type'] = $this->getFileType($file);
            unset($data['file']);
        }

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
     * Get product images with relationships loaded.
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
     * Get product image by ID with relationships.
     *
     * @param int|string $id The product image ID.
     * @param array $relations Relations to load.
     * @return \App\Models\ProductImage
     */
    public function findByIdWithRelations(int|string $id, array $relations = []): \App\Models\ProductImage
    {
        return $this->repository->findByIdWithRelations($id, $relations);
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
        return $this->repository->getByIds($ids, $perPage);
    }
}
