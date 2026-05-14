<?php

namespace App\Repositories\Eloquent;

use App\Models\CartItem;
use App\Models\Product;
use App\Repositories\Interfaces\InventoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class InventoryRepository implements InventoryRepositoryInterface
{
    /**
     * Check if cart items have sufficient stock.
     * Bulk Validation.
     * Validate entire cart before checkout Input.
     * (التأكد من صحة سلة التسوق قبل اتمام عملية الشراء)
     *
     * @param \Illuminate\Database\Eloquent\Collection $cartItems
     * @return array
     */
    public function validateCartItems($cartItems): array
    {
        $unavailableItems = [];
        $allAvailable = true;

        foreach ($cartItems as $cartItem) {
            $product = $cartItem->product;
            
            if (!$product || $product->stock < $cartItem->quantity) {
                $unavailableItems[] = [
                    'product_id' => $cartItem->product_id,
                    'product_name' => $product ? $product->name : 'Product not found',
                    'requested_quantity' => $cartItem->quantity,
                    'available_stock' => $product ? $product->stock : 0,
                ];
                $allAvailable = false;
            }
        }

        return [
            'all_available' => $allAvailable,
            'unavailable_items' => $unavailableItems,
        ];
    }

    /**
     * Reserve stock for cart items.
     *
     * @param \Illuminate\Database\Eloquent\Collection $cartItems
     * @return bool
     */
    public function reserveStock($cartItems): bool
    {
        try {
            Product::reserveStock($cartItems);
            return true;
        } catch (\Exception $e) {
            throw new \Exception('Failed to reserve stock: ' . $e->getMessage());
        }
    }

    /**
     * Reduce stock after successful payment.
     *
     * @param \Illuminate\Database\Eloquent\Collection $cartItems
     * @return bool
     */
    public function reduceStock($cartItems): bool
    {
        try {
            Product::reduceStock($cartItems);
            return true;
        } catch (\Exception $e) {
            throw new \Exception('Failed to reduce stock: ' . $e->getMessage());
        }
    }

    /**
     * Restore stock for cancelled orders.
     *
     * @param \Illuminate\Database\Eloquent\Collection $cartItems
     * @return bool
     */
    public function restoreStock($cartItems): bool
    {
        try {
            Product::restoreStock($cartItems);
            return true;
        } catch (\Exception $e) {
            throw new \Exception('Failed to restore stock: ' . $e->getMessage());
        }
    }

    /**
     * Check if product has sufficient stock.
     * Single Item Check.
     * Check individual product stock before adding to cart.
     *
     * @param int $productId
     * @param int $quantity
     * @return bool
     */
    public function checkStockAvailability(int $productId, int $quantity): bool
    {
        try {
            $product = Product::query()->find($productId);
            return $product ? $product->stock >= $quantity : false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if product has sufficient stock considering existing cart items.
     * Single Item Check with Cart Context.
     * Check individual product stock before adding to cart, considering existing quantity.
     *
     * @param int $productId
     * @param int $quantity
     * @param int|null $cartId
     * @return bool
     */
    public function checkStockAvailabilityWithCart(int $productId, int $quantity, ?int $cartId = null): bool
    {
        try {
            $product = Product::query()->find($productId);
            if (!$product) {
                return false;
            }

            $existingQuantity = 0;
            if ($cartId) {
                $existingCartItem = CartItem::where('cart_id', $cartId)
                    ->where('product_id', $productId)
                    ->first();
                $existingQuantity = $existingCartItem ? $existingCartItem->quantity : 0;
            }

            $totalRequestedQuantity = $existingQuantity + $quantity;
            return $product->stock >= $totalRequestedQuantity;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get current stock for a product.
     *
     * @param int $productId
     * @return int
     */
    public function getProductStock(int $productId): int
    {
        try {
            $product = Product::query()->find($productId);
            return $product ? $product->stock : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
}
