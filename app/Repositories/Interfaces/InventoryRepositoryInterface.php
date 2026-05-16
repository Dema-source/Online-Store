<?php

namespace App\Repositories\Interfaces;

use App\Models\CartItem;

interface InventoryRepositoryInterface
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
    public function validateCartItems($cartItems): array;

    /**
     * Reserve stock for cart items.
     *
     * @param \Illuminate\Database\Eloquent\Collection $cartItems
     * @return bool
     */
    public function reserveStock($cartItems): bool;

    /**
     * Reduce stock after successful payment.
     *
     * @param \Illuminate\Database\Eloquent\Collection $cartItems
     * @return bool
     */
    public function reduceStock($cartItems): bool;

    /**
     * Restore stock for cancelled orders.
     *
     * @param \Illuminate\Database\Eloquent\Collection $cartItems
     * @return bool
     */
    public function restoreStock($cartItems): bool;

    /**
     * Check if product has sufficient stock.
     * Single Item Check.
     * Check individual product stock before adding to cart.
     *
     * @param int $productId
     * @param int $quantity
     * @return bool
     */
    public function checkStockAvailability(int $productId, int $quantity): bool;

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
    public function checkStockAvailabilityWithCart(int $productId, int $quantity, ?int $cartId = null): bool;

    /**
     * Get current stock for a product.
     *
     * @param int $productId
     * @return int
     */
    public function getProductStock(int $productId): int;
}
