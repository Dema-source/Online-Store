<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Product\StoreProductRequest;
use App\Http\Requests\Api\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * ProductController Constructor.
     *
     * @param ProductService $service.
     */
    public function __construct(
        protected ProductService $service
    ) {}

    /**
     * Display a paginated listing of Products.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate(ProductResource::collection($data), 'Product list fetched successfully');
    }

    /**
     * Store a newly created Product in storage.
     *
     * @param StoreProductRequest $request The validated form request.
     * @return JsonResponse
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return $this->success(new ProductResource($item), 'Product created successfully');
    }

    /**
     * Display the specified Product.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function show(int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);

        return $this->success(new ProductResource($item), 'Product fetched successfully');
    }

    /**
     * Update the specified Product in storage.
     * 
     * @param UpdateProductRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function update(UpdateProductRequest $request, int|string $id): JsonResponse
    {
        $item = $this->service->update($id, $request->validated());

        return $this->success(new ProductResource($item), 'Product updated successfully');
    }

    /**
     * Remove the specified Product from storage.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->success(null, 'Product deleted successfully');
    }

    /**
     * Display a paginated listing of Products with relations.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function indexWithRelations(Request $request): JsonResponse
    {
        $relations = $request->input('relations', ['categories', 'images']);
        $filters = $request->except(['page', 'per_page', 'relations']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getAllWithRelations($relations, $filters, $perPage);

        return $this->paginate($data, 'Product list with relations fetched successfully');
    }

    /**
     * Display the specified Product with relations.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function showWithRelations(int|string $id, Request $request): JsonResponse
    {
        $relations = $request->input('relations', ['categories', 'images']);
        $item = $this->service->findByIdWithRelations($id, $relations);

        return $this->success($item, 'Product with relations fetched successfully');
    }

    /**
     * Get Products by multiple IDs.
     *
     * @param Request $request The HTTP request containing IDs.
     * @return JsonResponse
     */
    public function indexByIds(Request $request): JsonResponse
    {
        $ids = $request->input('ids', []);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getByIds($ids, $perPage);

        return $this->paginate(ProductResource::collection($data), 'Products by IDs fetched successfully');
    }
}
