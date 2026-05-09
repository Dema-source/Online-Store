<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Cart\StoreCartRequest;
use App\Http\Requests\Api\Cart\UpdateCartRequest;
use App\Http\Resources\CartResource;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * CartController Constructor.
     *
     * @param CartService $service.
     */
    public function __construct(
        protected CartService $service
    ) {}

    /**
     * Display a paginated listing of Carts.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate(CartResource::collection($data), 'Cart list fetched successfully');
    }

    /**
     * Store a newly created Cart in storage.
     *
     * @param StoreCartRequest $request The validated form request.
     * @return JsonResponse
     */
    public function store(StoreCartRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return $this->success(new CartResource($item), 'Cart created successfully');
    }

    /**
     * Display the specified Cart.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function show(int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);

        return $this->success(new CartResource($item), 'Cart fetched successfully');
    }

    /**
     * Update the specified Cart in storage.
     * 
     * @param UpdateCartRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function update(UpdateCartRequest $request, int|string $id): JsonResponse
    {
        $item = $this->service->update($id, $request->validated());

        return $this->success(new CartResource($item), 'Cart updated successfully');
    }

    /**
     * Remove the specified Cart from storage.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->success(null, 'Cart deleted successfully');
    }

    /**
     * Display a paginated listing of Carts with relationships.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function indexWithRelations(Request $request): JsonResponse
    {
        $relations = $request->input('relations', ['profile']);
        $filters = $request->except(['page', 'per_page', 'relations']);
        $perPage = (int) $request->input('per_page', 15);

        if (is_string($relations)) {
            $relations = explode(',', $relations);
        }

        $data = $this->service->getAllWithRelations($relations, $filters, $perPage);

        return $this->paginate(CartResource::collection($data), 'Cart list with relations fetched successfully');
    }

    /**
     * Display the specified Cart with relationships.
     *
     * @param int|string $id The primary key value.
     * @param Request $request The HTTP request.
     * @return JsonResponse
     */
    public function showWithRelations(int|string $id, Request $request): JsonResponse
    {
        $relations = $request->input('relations', ['profile']);

        if (is_string($relations)) {
            $relations = explode(',', $relations);
        }

        $item = $this->service->findByIdWithRelations($id, $relations);

        return $this->success(new CartResource($item), 'Cart fetched successfully with relations');
    }

    /**
     * Get carts by multiple IDs.
     *
     * @param Request $request The HTTP request.
     * @return JsonResponse
     */
    public function indexByIds(Request $request): JsonResponse
    {
        $ids = $request->input('ids', []);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getByIds($ids, $perPage);

        return $this->paginate(CartResource::collection($data), 'Carts fetched successfully by IDs');
    }

    /**
     * Find a cart by profile ID.
     *
     * @param int $profileId The profile ID.
     * @return JsonResponse
     */
    public function findByProfileId(int $profileId): JsonResponse
    {
        $item = $this->service->findByProfileId($profileId);

        if (!$item) {
            return $this->error('Cart not found for this profile', 404);
        }

        return $this->success(new CartResource($item), 'Cart fetched successfully by profile ID');
    }

}
