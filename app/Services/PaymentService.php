<?php

namespace App\Services;

use App\Models\Payment;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Service layer for handling business logic related to the "PaymentRepositoryInterface" repository.
 */
class PaymentService
{
    /**
     * PaymentService Constructor.
     *
     * @param \App\Repositories\Interfaces\PaymentRepositoryInterface $repository
     */
    public function __construct(
        protected PaymentRepositoryInterface $repository
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
     * Update payment status with automatic paid_at timestamp.
     *
     * @param int $paymentId
     * @param string $status
     * @return bool
     */
    public function updatePaymentStatus(int $paymentId, string $status): bool
    {
        return $this->repository->updatePaymentStatus($paymentId, $status);
    }

    /**
     * Get payment details by order ID.
     *
     * @param int $orderId
     * @return Payment|null
     */
    public function getPaymentByOrderId(int $orderId): ?Payment
    {
        return $this->repository->getPaymentByOrderId($orderId);
    }

    /**
     * Get payment status.
     *
     * @param int $paymentId
     * @return string|null
     */
    public function getPaymentStatus(int $paymentId): ?string
    {
        return $this->repository->getPaymentStatus($paymentId);
    }

    /**
     * Get payments for a specific profile.
     *
     * @param array $filters Optional filters.
     * @param int $perPage Number of items per page.
     * @param int $profileId The profile ID.
     * @return LengthAwarePaginator
     */
    public function getMyPayments(array $filters = [], int $perPage = 15, int $profileId): LengthAwarePaginator
    {
        $filters['profile_id'] = $profileId;
        return $this->repository->getAll($filters, $perPage);
    }
}