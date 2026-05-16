<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProductImage\StoreProductImageRequest;
use App\Http\Requests\Api\ProductImage\UpdateProductImageRequest;
use App\Http\Resources\ProductImageResource;
use App\Services\ProductImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductImageController extends Controller
{
    /**
     * ProductImageController Constructor.
     *
     * @param ProductImageService $service.
     */
    public function __construct(
        protected ProductImageService $service
    ) {}

    /**
     * Display a paginated listing of ProductImages.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate(ProductImageResource::collection($data), 'ProductImage list fetched successfully');
    }

    /**
     * Store a newly created ProductImage in storage.
     *
     * @param StoreProductImageRequest $request The validated form request.
     * @return JsonResponse
     */
    public function store(StoreProductImageRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return $this->success(new ProductImageResource($item), 'ProductImage created successfully');
    }

    /**
     * Display the specified ProductImage.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function show(int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);

        return $this->success(new ProductImageResource($item), 'ProductImage fetched successfully');
    }

    /**
     * Update the specified ProductImage in storage.
     * 
     * @param UpdateProductImageRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function update(UpdateProductImageRequest $request, int|string $id): JsonResponse
    {
        $item = $this->service->update($id, $request->validated());

        return $this->success(new ProductImageResource($item), 'ProductImage updated successfully');
    }

    /**
     * Remove the specified ProductImage from storage.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->success(null, 'ProductImage deleted successfully');
    }

    /**
     * Display a paginated listing of ProductImages with relations.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function indexWithRelations(Request $request): JsonResponse
    {
        $relations = $request->input('relations', ['product']);
        $filters = $request->except(['page', 'per_page', 'relations']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getAllWithRelations($relations, $filters, $perPage);

        return $this->paginate($data, 'ProductImage list with relations fetched successfully');
    }

    /**
     * Display the specified ProductImage with relations.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function showWithRelations(int|string $id, Request $request): JsonResponse
    {
        $relations = $request->input('relations', ['product']);
        $item = $this->service->findByIdWithRelations($id, $relations);

        return $this->success($item, 'ProductImage with relations fetched successfully');
    }

    /**
     * Get ProductImages by multiple IDs.
     *
     * @param Request $request The HTTP request containing IDs.
     * @return JsonResponse
     */
    public function indexByIds(Request $request): JsonResponse
    {
        $ids = $request->input('ids', []);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getByIds($ids, $perPage);

        return $this->paginate(ProductImageResource::collection($data), 'ProductImages by IDs fetched successfully');
    }
}
