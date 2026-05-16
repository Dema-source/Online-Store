<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Order\StoreOrderRequest;
use App\Http\Requests\Api\Order\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * OrderController Constructor.
     *
     * @param OrderService $service.
     */
    public function __construct(
        protected OrderService $service
    ) {}

    /**
     * Display a paginated listing of Orders.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getAll($filters, $perPage);

        return $this->success(OrderResource::collection($data), 'Order list fetched successfully');
    }

    /**
     * Display the specified Order.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function show(int|string $id): JsonResponse
    {
        $isAdmin = Auth::user()->hasRole('super_administrator');
        $this->getProfileIdForOrder($id, $isAdmin);

        $item = $this->service->findById($id);

        return $this->success(new OrderResource($item), 'Order fetched successfully');
    }

    /**
     * Update the specified Cart in storage.
     *
     * @param UpdateOrderRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function update(UpdateOrderRequest $request, int|string $id): JsonResponse
    {
        $isAdmin = Auth::user()->hasRole('super_administrator');
        $this->getProfileIdForOrder($id, $isAdmin);

        $item = $this->service->update($id, $request->validated());

        return $this->success(new OrderResource($item), 'Order updated successfully');
    }

    /**
     * Remove the specified Order from storage.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->success(null, 'Order deleted successfully');
    }

    /**
     * Get orders by multiple IDs.
     *
     * @param Request $request The HTTP request containing IDs.
     * @return JsonResponse
     */
    public function indexByIds(Request $request): JsonResponse
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|min:1'
        ]);

        $ids = $request->input('ids', []);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getByIds($ids, $perPage);

        return $this->success(OrderResource::collection($data), 'Orders fetched successfully by IDs');
    }

    /**
     * Get order with details.
     *
     * @param int $orderId The order ID.
     * @return JsonResponse
     */
    public function getDetails(int $orderId): JsonResponse
    {
        $details = $this->service->getOrderWithDetails($orderId);

        if (empty($details)) {
            return $this->error('Order not found', 404);
        }

        return $this->success($details, 'Order details fetched successfully');
    }

    /**
     * Update order status (admin only).
     *
     * @param Request $request The HTTP request.
     * @param int $orderId The order ID.
     * @return JsonResponse
     */
    public function updateStatus(Request $request, int $orderId): JsonResponse
    {
        if (!Auth::user()->hasRole('super_administrator')) {
            return $this->error('Only administrators can update order status', 403);
        }

        $request->validate([
            'status' => 'required|string|in:pending,confirmed,cancelled'
        ]);

        $updated = $this->service->updateOrderStatus($orderId, $request->status);

        if ($updated) {
            return $this->success(null, 'Order status updated successfully');
        }

        return $this->error('Failed to update order status', 500);
    }

    /**
     * Get order status.
     *
     * @param int $orderId The order ID.
     * @return JsonResponse
     */
    public function getStatus(int $orderId): JsonResponse
    {
        $isAdmin = Auth::user()->hasRole('super_administrator');
        $this->getProfileIdForOrder($orderId, $isAdmin);

        $status = $this->service->getOrderStatus($orderId);

        if ($status === null) {
            return $this->error('Order not found', 404);
        }

        return $this->success(['status' => $status], 'Order status fetched successfully');
    }

    /**
     * Check if order exists.
     *
     * @param int $orderId The order ID.
     * @return JsonResponse
     */
    public function checkExists(int $orderId): JsonResponse
    {
        $exists = $this->service->orderExists($orderId);

        return $this->success(['exists' => $exists], 'Order existence checked successfully');
    }

    /**
     * Cancel an order.
     *
     * @param int $orderId The order ID.
     * @return JsonResponse
     */
    public function cancel(int $orderId): JsonResponse
    {
        $isAdmin = Auth::user()->hasRole('super_administrator');
        $profileId = $this->getProfileIdForOrder($orderId, $isAdmin);

        $result = $this->service->cancelOrder($orderId, $profileId, $isAdmin);

        if ($result['success']) {
            return $this->success(null, $result['message']);
        }

        return $this->error($result['message'], 400);
    }

    /**
     * Get all orders for the authenticated customer.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function indexMyOrders(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);
        $profileId = Auth::user()->profile->id;

        $data = $this->service->getMyOrders($filters, $perPage, $profileId);

        return $this->success(OrderResource::collection($data), 'My orders fetched successfully');
    }

    /**
     * Check order ownership and return profile ID.
     *
     * @param int $orderId The order ID.
     * @param bool $isAdmin Whether the user is an admin.
     * @return int The profile ID.
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    private function getProfileIdForOrder(int $orderId, bool $isAdmin = false): int
    {
        if ($isAdmin) {
            $order = Order::findOrFail($orderId);
            return $order->profile_id;
        }

        $profileId = Auth::user()->profile->id;
        $order = Order::find($orderId);

        if (!$order || $order->profile_id !== $profileId) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                $this->error('You can only access your own orders', 403)
            );
        }

        return $profileId;
    }
}
