<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Repositories\Interfaces\CheckoutRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class CheckoutRepository implements CheckoutRepositoryInterface
{
    /**
     * Create a new order with its items.
     *
     * @param array $orderData
     * @return Order
     */
    public function createOrder(array $orderData): Order
    {
        return DB::transaction(function () use ($orderData) {
            $order = Order::create($orderData);
            
            if (isset($orderData['items']) && !empty($orderData['items'])) {
                $this->createOrderItems($order->id, $orderData['items']);
            }

            return $order;
        });
    }

    /**
     * Create order items for an order.
     *
     * @param int $orderId
     * @param array $items
     * @return bool
     */
    public function createOrderItems(int $orderId, array $items): bool
    {
        foreach ($items as $item) {
            OrderItem::create([
                'order_id' => $orderId,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price_at_ordered' => $item['price_at_ordered'],
            ]);
        }

        return true;
    }

    /**
     * Create a payment record for an order.
     *
     * @param array $paymentData
     * @return Payment
     */
    public function createPayment(array $paymentData): Payment
    {
        return Payment::create($paymentData);
    }

    /**
     * Get order details by ID.
     *
     * @param int $orderId
     * @return Order|null
     */
    public function getOrderById(int $orderId): ?Order
    {
        return Order::byId($orderId)->first();
    }

    /**
     * Get payment details by order ID.
     *
     * @param int $orderId
     * @return Payment|null
     */
    public function getPaymentByOrderId(int $orderId): ?Payment
    {
        return Payment::query()->where('order_id', '=', $orderId)->first();
    }
}
