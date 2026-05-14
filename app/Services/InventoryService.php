<?php

namespace App\Services;

use App\Repositories\Interfaces\InventoryRepositoryInterface;

class InventoryService
{
    protected InventoryRepositoryInterface $inventoryRepository;

    public function __construct(InventoryRepositoryInterface $inventoryRepository)
    {
        $this->inventoryRepository = $inventoryRepository;
    }

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
        return $this->inventoryRepository->validateCartItems($cartItems);
    }

    /**
     * Reserve stock for cart items.
     *
     * @param \Illuminate\Database\Eloquent\Collection $cartItems
     * @return bool
     */
    public function reserveStock($cartItems): bool
    {
        return $this->inventoryRepository->reserveStock($cartItems);
    }

    /**
     * Reduce stock after successful payment.
     *
     * @param \Illuminate\Database\Eloquent\Collection $cartItems
     * @return bool
     */
    public function reduceStock($cartItems): bool
    {
        return $this->inventoryRepository->reduceStock($cartItems);
    }

    /**
     * Restore stock for cancelled orders.
     *
     * @param \Illuminate\Database\Eloquent\Collection $cartItems
     * @return bool
     */
    public function restoreStock($cartItems): bool
    {
        return $this->inventoryRepository->restoreStock($cartItems);
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
        return $this->inventoryRepository->checkStockAvailability($productId, $quantity);
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
        return $this->inventoryRepository->checkStockAvailabilityWithCart($productId, $quantity, $cartId);
    }

    /**
     * Get current stock for a product.
     *
     * @param int $productId
     * @return int
     */
    public function getProductStock(int $productId): int
    {
        return $this->inventoryRepository->getProductStock($productId);
    }
}
