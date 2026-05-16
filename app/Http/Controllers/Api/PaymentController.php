<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Payment\StorePaymentRequest;
use App\Http\Requests\Api\Payment\UpdatePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * PaymentController Constructor.
     *
     * @param PaymentService $service.
     */
    public function __construct(
        protected PaymentService $service
    ) {}

    /**
     * Display a paginated listing of Payments.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getAll($filters, $perPage);

        return $this->success(PaymentResource::collection($data), 'Payment list fetched successfully');
    }

    /**
     * Display the specified Payment.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function show(int|string $id): JsonResponse
    {
        $isAdmin = Auth::user()->hasRole('super_administrator');
        $this->getProfileIdForPayment($id, $isAdmin);

        $item = $this->service->findById($id);

        return $this->success(new PaymentResource($item), 'Payment fetched successfully');
    }

    /**
     * Remove the specified Payment from storage.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->success(null, 'Payment deleted successfully');
    }

    /**
     * Update payment status.
     *
     * @param Request $request The HTTP request.
     * @param int $paymentId The payment ID.
     * @return JsonResponse
     */
    public function updateStatus(Request $request, int $paymentId): JsonResponse
    {
        $request->validate([
            'status' => 'required|string|in:pending,paid,cancelled'
        ]);

        $updated = $this->service->updatePaymentStatus($paymentId, $request->status);

        if ($updated) {
            return $this->success(null, 'Payment status updated successfully');
        }

        return $this->error('Failed to update payment status', 500);
    }

    /**
     * Get payment by order ID.
     *
     * @param int $orderId The order ID.
     * @return JsonResponse
     */
    public function getByOrderId(int $orderId): JsonResponse
    {
        $payment = $this->service->getPaymentByOrderId($orderId);

        if (!$payment) {
            return $this->error('Payment not found for this order', 404);
        }

        return $this->success(new PaymentResource($payment), 'Payment fetched successfully');
    }

    /**
     * Get payment status.
     *
     * @param int $paymentId The payment ID.
     * @return JsonResponse
     */
    public function getStatus(int $paymentId): JsonResponse
    {
        $isAdmin = Auth::user()->hasRole('super_administrator');
        $this->getProfileIdForPayment($paymentId, $isAdmin);

        $status = $this->service->getPaymentStatus($paymentId);

        if ($status === null) {
            return $this->error('Payment not found', 404);
        }

        return $this->success(['status' => $status], 'Payment status fetched successfully');
    }

    /**
     * Get all payments for the authenticated customer.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function indexMyPayment(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);
        $profileId = Auth::user()->profile->id;

        $data = $this->service->getMyPayments($filters, $perPage, $profileId);

        return $this->success(PaymentResource::collection($data), 'My payments fetched successfully');
    }

    /**
     * Check payment ownership and return profile ID.
     *
     * @param int $paymentId The payment ID.
     * @param bool $isAdmin Whether the user is an admin.
     * @return int The profile ID.
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    private function getProfileIdForPayment(int $paymentId, bool $isAdmin = false): int
    {
        if ($isAdmin) {
            $payment = \App\Models\Payment::findOrFail($paymentId);
            return $payment->order->profile_id;
        }

        $profileId = Auth::user()->profile->id;
        $payment = \App\Models\Payment::find($paymentId);

        if (!$payment || !$payment->order || $payment->order->profile_id !== $profileId) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                $this->error('You can only access your own payments', 403)
            );
        }

        return $profileId;
    }
}