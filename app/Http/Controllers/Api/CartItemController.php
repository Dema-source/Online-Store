<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CartItem\StoreCartItemRequest;
use App\Http\Requests\Api\CartItem\UpdateCartItemRequest;
use App\Http\Requests\Api\CartItem\CheckProductsInCartRequest;
use App\Http\Resources\CartItemResource;
use App\Services\CartItemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartItemController extends Controller
{
    /**
     * CartItemController Constructor.
     *
     * @param CartItemService $service.
     * @param \App\Repositories\Interfaces\CartItemRepositoryInterface $repository
     */
    public function __construct(
        protected CartItemService $service,
        protected \App\Repositories\Interfaces\CartItemRepositoryInterface $repository
    ) {}

     // For Admin
    /**
     * Display a paginated listing of CartItems.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate($data, 'CartItem list fetched successfully');
    }

    /**
     * Store a newly created CartItem in storage.
     *
     * @param StoreCartItemRequest $request The validated form request.
     * @return JsonResponse
     */
    public function store(StoreCartItemRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return $this->success(new CartItemResource($item), 'CartItem created successfully');
    }

    /**
     * Display the specified CartItem.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function show(int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);

        return $this->success(new CartItemResource($item), 'CartItem fetched successfully');
    }

    /**
     * Update a specified CartItem in storage.
     *
     * @param UpdateCartItemRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function update(UpdateCartItemRequest $request, int|string $id): JsonResponse
    {
        $item = $this->service->update($id, $request->validated());

        return $this->success(new CartItemResource($item), 'CartItem updated successfully');
    }

    /**
     * Remove specified CartItem from storage.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->success(null, 'CartItem deleted successfully');
    }

    /**
     * Clear all items from a cart (Admin).
     *
     * @param int|string $cartId The cart ID.
     * @return JsonResponse
     */
    public function clearCart(int|string $cartId): JsonResponse
    {
        $deletedCount = $this->service->clearCart($cartId);

        return $this->success(['deleted_count' => $deletedCount], 'Cart cleared successfully');
    }

    /**
     * Get cart total value (Admin).
     *
     * @param int|string $cartId The cart ID.
     * @return JsonResponse
     */
    public function getCartTotal(int|string $cartId): JsonResponse
    {
        $total = $this->service->getCartTotal($cartId);

        return $this->success(['cart_total' => $total], 'Cart total retrieved successfully');
    }

    /**
     * Get cart items count.
     *
     * @param int|string $cartId The cart ID.
     * @return JsonResponse
     */
    public function getCartItemsCount(int|string $cartId): JsonResponse
    {
        $count = $this->service->getCartItemsCount($cartId);

        return $this->success(['items_count' => $count], 'Cart items count retrieved successfully');
    }

    /**
     * Check if products are in cart.
     *
     * @param CheckProductsInCartRequest $request
     * @return JsonResponse
     */
    public function checkProductsInCart(CheckProductsInCartRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $cartId = $validated['cart_id'];
        $productIds = $validated['product_ids'];

        $existingItems = $this->service->checkProductsInCart($cartId, $productIds);

        return $this->success($existingItems, 'Products check completed successfully');
    }

    // For Customer
    /**
     * Display customer's cart items.
     *
     * @return JsonResponse
     */
    public function indexForCustomer(): JsonResponse
    {
        $cartId = auth()->user()->profile->cart->id ?? null;

        if (!$cartId) {
            return $this->error('Cart not found', 404);
        }

        $items = $this->service->getByCartId($cartId);

        return $this->success(CartItemResource::collection($items), 'Cart items retrieved successfully');
    }

    /**
     * Store a newly created CartItem in storage (Customer).
     *
     * @param StoreCartItemRequest $request The validated form request.
     * @return JsonResponse
     */
    public function storeForCustomer(StoreCartItemRequest $request): JsonResponse
    {
        $data = $request->validated();
        $cartId = auth()->user()->profile->cart->id ?? null;

        if (!$cartId) {
            return $this->error('You do not have an active cart. Please create a cart first.', 404);
        }

        $data['cart_id'] = $cartId;
        $item = $this->service->create($data);

        return $this->success(new CartItemResource($item), 'CartItem created successfully');
    }

    /**
     * Update cart item quantity by product ID (Customer-friendly).
     *
     * @param UpdateCartItemRequest $request
     * @return JsonResponse
     */
    public function updateCartItemForCustomer(UpdateCartItemRequest $request): JsonResponse
    {
        $data = $request->validated();
        $cartId = auth()->user()->profile->cart->id ?? null;

        if (!$cartId) {
            return $this->error('Cart not found', 404);
        }

        // Find the cart item by cart_id and product_id
        $cartItem = $this->repository->findByCartAndProduct($cartId, $data['product_id']);

        if (!$cartItem) {
            return $this->error('This product is not in your cart.', 404);
        }

        // Update the cart item
        $item = $this->service->update($cartItem->id, ['quantity' => $data['quantity']]);

        return $this->success(new CartItemResource($item), 'CartItem updated successfully');
    }

    /**
     * Clear all items from customer's own cart.
     *
     * @return JsonResponse
     */
    public function clearMyCart(): JsonResponse
    {
        // Get customer's cart ID from authenticated user
        $cartId = auth()->user()->profile->cart->id ?? null;

        if (!$cartId) {
            return $this->error('You do not have an active cart.', 404);
        }

        $deletedCount = $this->service->clearCart($cartId);

        return $this->success(['deleted_count' => $deletedCount], 'Your cart cleared successfully');
    }

    /**
     * Check if products are in customer's cart.
     *
     * @param CheckProductsInCartRequest $request
     * @return JsonResponse
     */
    public function checkProductsInMyCart(CheckProductsInCartRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $cartId = auth()->user()->profile->cart->id ?? null;
        $productIds = $validated['product_ids'];

        if (!$cartId) {
            return $this->error('User cart not found', 404);
        }

        $existingItems = $this->service->checkProductsInCart($cartId, $productIds);

        return $this->success($existingItems, 'Products check completed successfully');
    }

    /**
     * Get customer's cart total value.
     *
     * @return JsonResponse
     */
    public function getMyCartTotal(): JsonResponse
    {
        // Get customer's cart ID from authenticated user
        $cartId = auth()->user()->profile->cart->id ?? null;

        if (!$cartId) {
            return $this->error('You do not have an active cart.', 404);
        }

        $total = $this->service->getCartTotal($cartId);

        return $this->success(['cart_total' => $total], 'Your cart total retrieved successfully');
    }

    /**
     * Get customer's cart items count.
     *
     * @return JsonResponse
     */
    public function getMyCartItemsCount(): JsonResponse
    {
        $cartId = auth()->user()->profile->cart->id ?? null;

        if (!$cartId) {
            return $this->error('User cart not found', 404);
        }

        $count = $this->service->getCartItemsCount($cartId);

        return $this->success(['items_count' => $count], 'Cart items count retrieved successfully');
    }
}
