<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderRepository implements OrderRepositoryInterface
{
    /**  
     * Dependency injection of the Eloquent model.  
     *  
     * @param Order $model  
     */
    public function __construct(
        protected Order $model
    ) {}

    /**  
     * Get a paginated list of records applying optional filters.  
     *  
     * @param array $filters Key/value filters to apply to the query.  
     * @param int $perPage Number of items per page.  
     * @return LengthAwarePaginator  
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->query();

        foreach ($filters as $field => $value) {
            if ($value !== null && $value !== '') {
                switch ($field) {
                    case 'search':
                        // Handle search term across multiple fields
                        $query->search($value);
                        break;

                    case 'created_on':
                        // Handle created on date filter
                        $query->createdOn($value);
                        break;

                    case 'created_from':
                        // Handle created from date filter
                        $query->createdFrom($value);
                        break;

                    case 'created_to':
                        // Handle created to date filter
                        $query->createdTo($value);
                        break;

                    case 'created_this_month':
                        // Handle created this month filter
                        $query->createdThisMonth();
                        break;

                    case 'order_date':
                        // Handle order date filter
                        $query->byOrderDate($value);
                        break;

                    case 'order_date_from':
                        // Handle order date from filter
                        $query->orderDateFrom($value);
                        break;

                    case 'order_date_to':
                        // Handle order date to filter
                        $query->orderDateTo($value);
                        break;

                    case 'min_total':
                        // Handle minimum total filter
                        $query->minTotal($value);
                        break;

                    case 'max_total':
                        // Handle maximum total filter
                        $query->maxTotal($value);
                        break;

                    default:
                        // Handle individual filter scopes
                        $query->where($field, $value);
                        break;
                }
            }
        }

        return $query->latest()->paginate($perPage);
    }

    /**  
     * Retrieve a single record by ID or throw an exception if not found.  
     *  
     * @param int|string $id  
     * @return Order  
     */
    public function findById(int|string $id): Order
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Update an existing record by ID with a given data.
     *
     * @param int|string $id The primary key value.
     * @param array $data.
     * @return Order
     */
    public function update(int|string $id, array $data): Order
    {
        $item = $this->findById($id);
        $item->update($data);

        return $item->fresh();
    }

    /**
     * Delete a record by ID.
     *
     * @param int|string $id The primary key value.
     * @return bool
     */
    public function delete(int|string $id): bool
    {
        $item = $this->findById($id);

        return (bool) $item->delete();
    }

    /**
     * Get order with its items and payment.
     *
     * @param int $orderId
     * @return array
     */
    public function getOrderWithDetails(int $orderId): array
    {
        $order = $this->model->with(['order_items.product', 'payment'])->find($orderId);

        if (!$order) {
            return [];
        }

        return [
            'order' => $order,
            'items' => $order->order_items,
            'payment' => $order->payment
        ];
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
        try {
            $order = $this->findById($orderId);
            $order->update(['status' => $status]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get order status.
     *
     * @param int $orderId
     * @return string|null
     */
    public function getOrderStatus(int $orderId): ?string
    {
        $order = $this->model->byId($orderId)->first();
        return $order ? $order->status : null;
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
        return $this->model->whereIn('id', $ids)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Check if order exists.
     *
     * @param int $orderId
     * @return bool
     */
    public function orderExists(int $orderId): bool
    {
        return $this->model->byId($orderId)->exists();
    }

    /**
     * Cancel an order by updating order and payment status.
     *
     * @param int $orderId
     * @return bool
     */
    public function cancelOrder(int $orderId): bool
    {
        try {
            $order = $this->model->with(['payment', 'order_items.product'])->find($orderId);

            if (!$order) {
                return false;
            }

            // Update order.status = cancelled
            $order->status = 'cancelled';
            $order->save();

            // Update payment.status = cancelled
            if ($order->payment) {
                $order->payment->status = 'cancelled';
                $order->payment->save();
            }

            // Restore stock
            \App\Models\Product::restoreStock($order->order_items);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
