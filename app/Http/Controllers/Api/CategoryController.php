<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Category\StoreCategoryRequest;
use App\Http\Requests\Api\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * CategoryController Constructor.
     *
     * @param CategoryService $service.
     */
    public function __construct(
        protected CategoryService $service
    ) {}

    /**
     * Display a paginated listing of Categorys.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate(CategoryResource::collection($data), 'Category list fetched successfully');
    }

    /**
     * Store a newly created Category in storage.
     *
     * @param StoreCategoryRequest $request The validated form request.
     * @return JsonResponse
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return $this->success(new CategoryResource($item), 'Category created successfully');
    }

    /**
     * Display the specified Category.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function show(int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);

        return $this->success(new CategoryResource($item), 'Category fetched successfully');
    }

    /**
     * Update the specified Category in storage.
     * 
     * @param UpdateCategoryRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function update(UpdateCategoryRequest $request, int|string $id): JsonResponse
    {
        $item = $this->service->update($id, $request->validated());

        return $this->success(new CategoryResource($item), 'Category updated successfully');
    }
 
    /**
     * Remove the specified Category from storage.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->success(null, 'Category deleted successfully');
    }

    /**
     * Display a paginated listing of Categories with relations.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function indexWithRelations(Request $request): JsonResponse
    {
        $relations = $request->input('relations', ['products']);
        $filters = $request->except(['page', 'per_page', 'relations']);

        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getAllWithRelations($relations, $filters, $perPage);

        return $this->paginate(CategoryResource::collection($data), 'Category list with relations fetched successfully');
    }

    /**
     * Display the specified Category with relations.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function showWithRelations(int|string $id, Request $request): JsonResponse
    {
        $relations = $request->input('relations', ['products']);
        $item = $this->service->findByIdWithRelations($id, $relations);

        return $this->success(new CategoryResource($item), 'Category with relations fetched successfully');
    }

    /**
     * Get Categories by multiple IDs.
     *
     * @param Request $request The HTTP request containing IDs.
     * @return JsonResponse
     */
    public function indexByIds(Request $request): JsonResponse
    {
        $ids = $request->input('ids', []);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getByIds($ids, $perPage);

        return $this->paginate(CategoryResource::collection($data), 'Categories by IDs fetched successfully');
    }

}
