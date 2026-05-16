<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Checkout\CheckoutRequest;
use App\Services\CheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    protected CheckoutService $checkoutService;

    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    /**
     * Process checkout for authenticated user.
     *
     * @param CheckoutRequest $request
     * @return JsonResponse
     */
    public function checkout(CheckoutRequest $request): JsonResponse
    {
        try {
            $result = $this->checkoutService->processCheckout(
                auth()->user()->profile,
                $request->validated()
            );

            return $this->success($result, 'Checkout completed successfully');
        } catch (\Exception $e) {
            return $this->error('Checkout failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get checkout status for an order.
     *
     * @param int $orderId
     * @return JsonResponse
     */
    public function getStatus(int $orderId): JsonResponse
    {
        try {
            $status = $this->checkoutService->getOrderStatus($orderId);
            
            return $this->success($status, 'Order status retrieved');
        } catch (\Exception $e) {
            return $this->error('Failed to get order status: ' . $e->getMessage(), 500);
        }
    }
}
