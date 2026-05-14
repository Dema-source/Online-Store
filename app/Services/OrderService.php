<?php

namespace App\Services;

use App\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Service layer for handling business logic related to the "OrderRepositoryInterface" repository.
 */
class OrderService
{
    /**
     * OrderService Constructor.
     *
     * @param \App\Repositories\Interfaces\OrderRepositoryInterface $repository
     */
    public function __construct(
        protected OrderRepositoryInterface $repository
    ) {}

    /**
     * Retrieve a paginated list of records applying optional dynamic filters.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getAll($filters, $perPage);
    }

    /**
     * Get orders by multiple IDs.
     *
     * @param array $ids Array of order IDs.
     * @param int $perPage Number of items per page.
     * @return LengthAwarePaginator
     */
    public function getByIds(array $ids, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getByIds($ids, $perPage);
    }

    /**
     * Get orders for a specific profile.
     *
     * @param array $filters Optional filters.
     * @param int $perPage Number of items per page.
     * @param int $profileId The profile ID.
     * @return LengthAwarePaginator
     */
    public function getMyOrders(array $filters = [], int $perPage = 15, int $profileId): LengthAwarePaginator
    {
        $filters['profile_id'] = $profileId;
        return $this->repository->getAll($filters, $perPage);
    }

    /**
     * Find a record by its ID.
     *
     * @param int|string $id
     * @return mixed
     */
    public function findById(int|string $id): mixed
    {
        return $this->repository->findById($id);
    }

    /**
     * Update an existing record by ID with the given data.
     *
     * @param int|string $id
     * @param array $data
     * @return mixed
     */
    public function update(int|string $id, array $data): mixed
    {
        $order = \App\Models\Order::with('payment')->find($id);

        if (!$order) {
            throw new \Exception('Order not found');
        }

        // Validate order status - cannot update cancelled orders
        if ($order->status === 'cancelled') {
            throw new \Exception('Cannot update a cancelled order');
        }

        // Validate payment status - cannot update if payment is paid or cancelled
        if ($order->payment) {
            if ($order->payment->status === 'paid') {
                throw new \Exception('Cannot update an order with paid payment');
            }
            if ($order->payment->status === 'cancelled') {
                throw new \Exception('Cannot update an order with cancelled payment');
            }
        }

        return $this->repository->update($id, $data);
    }

    /**
     * Delete a record by ID.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete(int|string $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Get order with its items and payment.
     *
     * @param int $orderId
     * @return array
     */
    public function getOrderWithDetails(int $orderId): array
    {
        return $this->repository->getOrderWithDetails($orderId);
    }

    /**
     * Update order status.
     *
     * @param int $orderId
     * @param string $status
     * @return bool
     */
    public function updateOrderStatus(int $orderId, string $status): bool
    {
        return $this->repository->updateOrderStatus($orderId, $status);
    }

    /**
     * Get order status.
     *
     * @param int $orderId
     * @return string|null
     */
    public function getOrderStatus(int $orderId): ?string
    {
        return $this->repository->getOrderStatus($orderId);
    }

    /**
     * Check if order exists.
     *
     * @param int $orderId
     * @return bool
     */
    public function orderExists(int $orderId): bool
    {
        return $this->repository->orderExists($orderId);
    }

    /**
     * Cancel an order with business logic validation.
     *
     * @param int $orderId
     * @param int $profileId The authenticated user's profile ID
     * @param bool $isAdmin Whether the user is an admin
     * @return array ['success' => bool, 'message' => string]
     */
    public function cancelOrder(int $orderId, int $profileId, bool $isAdmin = false): array
    {
        $order = \App\Models\Order::with(['payment'])->find($orderId);

        if (!$order) {
            return ['success' => false, 'message' => 'Order not found'];
        }

        // Check if order belongs to the authenticated user (skip for admins)
        if (!$isAdmin && $order->profile_id !== $profileId) {
            return ['success' => false, 'message' => 'You can only cancel your own orders'];
        }

        // Check order.status - only allow cancellation for pending or confirmed orders
        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return ['success' => false, 'message' => 'Order can only be cancelled if status is pending or confirmed'];
        }

        // Check payment.status
        if (!$order->payment) {
            return ['success' => false, 'message' => 'Payment not found for this order'];
        }

        if ($order->payment->status === 'paid') {
            return ['success' => false, 'message' => 'Order cannot be cancelled because payment is already paid'];
        }

        if ($order->payment->status !== 'pending') {
            return ['success' => false, 'message' => 'Order cannot be cancelled because payment status is not pending'];
        }

        // Allow cancellation - use database transaction for atomicity
        DB::beginTransaction();
        try {
            $result = $this->repository->cancelOrder($orderId);

            if ($result) {
                DB::commit();
                return ['success' => true, 'message' => 'Order cancelled successfully'];
            } else {
                DB::rollBack();
                return ['success' => false, 'message' => 'Failed to cancel order'];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => 'Failed to cancel order: ' . $e->getMessage()];
        }
    }
}
