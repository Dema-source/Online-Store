<?php

namespace App\Repositories\Eloquent;

use App\Models\CartItem;
use App\Repositories\Interfaces\CartItemRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CartItemRepository implements CartItemRepositoryInterface
{
    /**
     * CartItemRepository Constructor.
     *
     * @param CartItem $model
     */
    public function __construct(
        protected CartItem $model
    ) {}

    /**
     * Get all cart items with optional filters.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->query();

        foreach ($filters as $field => $value) {
            if ($value !== null && $value !== '') {
                switch ($field) {
                    case 'cart_id':
                        $query->byCartId($value);
                        break;
                    case 'cart_ids':
                        if (is_array($value)) {
                            $query->byCartIds($value);
                        }
                        break;
                    case 'product_id':
                        $query->byProductId($value);
                        break;
                    case 'product_ids':
                        if (is_array($value)) {
                            $query->byProductIds($value);
                        }
                        break;
                    case 'quantity':
                        $query->byQuantity($value);
                        break;
                    case 'quantity_min':
                        $query->byMinQuantity($value);
                        break;
                    case 'quantity_max':
                        $query->byMaxQuantity($value);
                        break;
                    case 'quantity_range':
                        if (is_array($value) && count($value) === 2) {
                            $query->byQuantityRange($value[0], $value[1]);
                        }
                        break;
                    default:
                        $query->where($field, $value);
                        break;
                }
            }
        }

        return $query->orderBy('id', 'desc')->paginate($perPage);
    }

    /**
     * Find cart item by ID.
     *
     * @param int|string $id
     * @return CartItem
     */
    public function findById(int|string $id): CartItem
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Create cart item.
     * Updates existing item if it already exists.
     *
     * @param array $data
     * @return CartItem
     */
    public function create(array $data): CartItem
    {
        $cartId = $data['cart_id'];
        $productId = $data['product_id'];
        $quantity = $data['quantity'] ?? 1;
        
        // Check if item already exists
        $existingItem = $this->findByCartAndProduct($cartId, $productId);
        
        if ($existingItem) {
            // Update existing item quantity
            $newQuantity = $existingItem->quantity + $quantity;
            return $this->update($existingItem->id, ['quantity' => $newQuantity]);
        } else {
            // Create new item
            return CartItem::create([
                'cart_id' => $cartId,
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        }
    }

    /**
     * Update cart item.
     *
     * @param int|string $id
     * @param array $data
     * @return CartItem
     */
    public function update(int|string $id, array $data): CartItem
    {
        $item = $this->findById($id);
        $item->update($data);

        return $item->fresh();
    }

    /**
     * Delete cart item.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete(int|string $id): bool
    {
        $item = $this->findById($id);
        return (bool) $item->delete();
    }

    /**
     * Get cart items with relations.
     *
     * @param array $relations
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllWithRelations(array $relations = [], array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with($relations);

        foreach ($filters as $field => $value) {
            if ($value !== null && $value !== '') {
                switch ($field) {
                    case 'cart_id':
                        $query->byCartId($value);
                        break;
                    case 'cart_ids':
                        if (is_array($value)) {
                            $query->byCartIds($value);
                        }
                        break;
                    case 'product_id':
                        $query->byProductId($value);
                        break;
                    case 'product_ids':
                        if (is_array($value)) {
                            $query->byProductIds($value);
                        }
                        break;
                    case 'quantity':
                        $query->byQuantity($value);
                        break;
                    case 'quantity_min':
                        $query->byMinQuantity($value);
                        break;
                    case 'quantity_max':
                        $query->byMaxQuantity($value);
                        break;
                    case 'quantity_range':
                        if (is_array($value) && count($value) === 2) {
                            $query->byQuantityRange($value[0], $value[1]);
                        }
                        break;
                    default:
                        $query->where($field, $value);
                        break;
                }
            }
        }

        return $query->orderBy('id', 'desc')->paginate($perPage);
    }

    /**
     * Find cart item by ID with relations.
     *
     * @param int|string $id
     * @param array $relations
     * @return CartItem
     */
    public function findByIdWithRelations(int|string $id, array $relations = []): CartItem
    {
        return $this->model->with($relations)->findOrFail($id);
    }

    /**
     * Get cart items by multiple IDs.
     *
     * @param array $ids
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByIds(array $ids, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->byIds($ids)->paginate($perPage);
    }

    /**
     * Find cart item by cart and product.
     *
     * @param int|string $cartId
     * @param int|string $productId
     * @return CartItem|null
     */
    public function findByCartAndProduct(int|string $cartId, int|string $productId): ?CartItem
    {
        return $this->model
            ->byCartId($cartId)
            ->byProductId($productId)
            ->first();
    }

    /**
     * Update or create cart item.
     *
     * @param int|string $cartId
     * @param int|string $productId
     * @param int $quantity
     * @return CartItem
     */
    public function updateOrCreate(int|string $cartId, int|string $productId, int $quantity): CartItem
    {
        return $this->model->updateOrCreate(
            ['cart_id' => $cartId, 'product_id' => $productId],
            ['quantity' => $quantity]
        );
    }

    /**
     * Clear all items from a cart.
     *
     * @param int|string $cartId
     * @return int
     */
    public function clearCart(int|string $cartId): int
    {
        return $this->model->byCartId($cartId)->delete();
    }

    /**
     * Get cart total value.
     *
     * @param int|string $cartId
     * @return float
     */
    public function getCartTotal(int|string $cartId): float
    {
        return $this->model->byCartId($cartId)
            ->with('product')
            ->get()
            ->sum(function ($cartItem) {
                return ($cartItem->product->price ?? 0) * $cartItem->quantity;
            });
    }

    /**
     * Get cart items count.
     *
     * @param int|string $cartId
     * @return int
     */
    public function getCartItemsCount(int|string $cartId): int
    {
        return $this->model->byCartId($cartId)->count();
    }

    /**
     * Get product carts count.
     *
     * @param int|string $productId
     * @return int
     */
    public function getProductCartsCount(int|string $productId): int
    {
        return $this->model->byProductId($productId)->count();
    }
    
    
    /**
     * Check if products are in cart.
     *
     * @param int|string $cartId
     * @param array $productIds
     * @return array
     */
    public function checkProductsInCart(int|string $cartId, array $productIds): array
    {
        $existingItems = $this->model
            ->where('cart_id', $cartId)
            ->whereIn('product_id', $productIds)
            ->get(['product_id', 'quantity']);

        return $existingItems->toArray();
    }
}
