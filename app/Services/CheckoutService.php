<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Repositories\Interfaces\CheckoutRepositoryInterface;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    protected CheckoutRepositoryInterface $checkoutRepository;
    protected InventoryService $inventoryService;

    public function __construct(
        CheckoutRepositoryInterface $checkoutRepository,
        InventoryService $inventoryService
    ) {
        $this->checkoutRepository = $checkoutRepository;
        $this->inventoryService = $inventoryService;
    }

    /**
     * Process checkout for authenticated user.
     *
     * @param mixed $profile
     * @param array $checkoutData
     * @return array
     */
    public function processCheckout($profile, array $checkoutData): array
    {
        return DB::transaction(function () use ($profile, $checkoutData) {
            try {
                // Get user's cart
                $cart = $profile->cart;
                if (!$cart || $cart->cart_items->isEmpty()) {
                    throw new \Exception('Cart is empty');
                }

                // Validate stock availability
                $this->inventoryService->validateCartItems($cart->cart_items);

                // Reserve stock
                $this->inventoryService->reserveStock($cart->cart_items);

                // Create order
                $orderData = [
                    'profile_id' => $profile->id,
                    'status' => 'confirmed',
                    'total_price' => $this->calculateTotal($cart->cart_items),
                    'shipping_address' => $checkoutData['shipping_address'],
                    'phone' => $checkoutData['phone'],
                    'order_date' => now(),
                ];

                $orderData['items'] = $this->prepareOrderItems($cart->cart_items);
                $order = $this->checkoutRepository->createOrder($orderData);

                // Create payment record
                $paymentData = [
                    'order_id' => $order->id,
                    'payment_method' => $checkoutData['payment_method'] ? $checkoutData['payment_method'] : 'cash_on_delivery',
                    'status' => 'pending',
                    'transaction_id' => $this->generateTransactionId(),
                    'amount' => $order->total_price,
                    'paid_at' => $checkoutData['payment_method'] === 'cash_on_delivery' ? null : now(),
                ];

                $payment = $this->checkoutRepository->createPayment($paymentData);

                // Clear cart
                $this->clearCart($cart);

                return [
                    'order' => $order,
                    'payment' => $payment,
                    'message' => 'Order created successfully'
                ];
            } catch (\Exception $e) {
                throw new \Exception('Checkout failed: ' . $e->getMessage());
            }
        });
    }

    /**
     * Get order status by ID.
     *
     * @param int $orderId
     * @return array
     */
    public function getOrderStatus(int $orderId): array
    {
        $order = $this->checkoutRepository->getOrderById($orderId);

        if (!$order) {
            return ['status' => 'not_found', 'message' => 'Order not found'];
        }

        return [
            'status' => $order->status,
            'order' => $order,
            'payment' => $this->checkoutRepository->getPaymentByOrderId($orderId)
        ];
    }

    /**
     * Calculate total price for cart items.
     *
     * @param \Illuminate\Database\Eloquent\Collection $cartItems
     * @return float
     */
    private function calculateTotal($cartItems): float
    {
        return $cartItems->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });
    }

    /**
     * Prepare order items from cart items.
     *
     * @param \Illuminate\Database\Eloquent\Collection $cartItems
     * @return array
     */
    private function prepareOrderItems($cartItems): array
    {
        return $cartItems->map(function ($cartItem) {
            return [
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity,
                'price_at_ordered' => $cartItem->product->price,
            ];
        })->toArray();
    }

    /**
     * Clear user cart after successful checkout.
     *
     * @param Cart $cart
     * @return void
     */
    private function clearCart(Cart $cart): void
    {
        $cart->cart_items()->delete();
    }

    /**
     * Generate a unique transaction ID for payments.
     *
     * @return string
     */
    private function generateTransactionId(): string
    {
        return 'TXN_' . strtoupper(uniqid()) . '_' . time();
    }
}
