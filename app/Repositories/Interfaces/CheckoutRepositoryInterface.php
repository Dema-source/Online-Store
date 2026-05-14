<?php

namespace App\Repositories\Interfaces;

use App\Models\Order;
use App\Models\Payment;

interface CheckoutRepositoryInterface
{
    /**
     * Create a new order with its items.
     *
     * @param array $orderData
     * @return Order
     */
    public function createOrder(array $orderData): Order;

    /**
     * Create order items for an order.
     *
     * @param int $orderId
     * @param array $items
     * @return bool
     */
    public function createOrderItems(int $orderId, array $items): bool;

    /**
     * Create a payment record for an order.
     *
     * @param array $paymentData
     * @return Payment
     */
    public function createPayment(array $paymentData): Payment;

    /**
     * Get order details by ID.
     *
     * @param int $orderId
     * @return Order|null
     */
    public function getOrderById(int $orderId): ?Order;

    /**
     * Get payment details by order ID.
     *
     * @param int $orderId
     * @return Payment|null
     */
    public function getPaymentByOrderId(int $orderId): ?Payment;
}
