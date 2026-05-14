<?php

namespace App\Repositories\Eloquent;

use App\Models\Payment;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PaymentRepository implements PaymentRepositoryInterface
{
    /**  
     * Dependency injection of the Eloquent model.  
     *  
     * @param Payment $model  
     */
    public function __construct(
        protected Payment $model
    ) {}

    /**
     * Get a paginated list of records applying optional filters using Payment model scopes.
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

                    case 'status':
                        // Handle payment status filter
                        $query->byStatus($value);
                        break;

                    case 'min_amount':
                        // Handle minimum amount filter
                        $query->minAmount($value);
                        break;

                    case 'max_amount':
                        // Handle maximum amount filter
                        $query->maxAmount($value);
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

                    case 'paid_on':
                        // Handle paid on date filter
                        $query->paidOn($value);
                        break;

                    case 'paid_from':
                        // Handle paid from date filter
                        $query->paidFrom($value);
                        break;

                    case 'paid_to':
                        // Handle paid to date filter
                        $query->paidTo($value);
                        break;

                    case 'profile_id':
                        // Handle profile_id filter through order relationship
                        $query->whereHas('order', function ($q) use ($value) {
                            $q->where('profile_id', $value);
                        });
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
     * @return Payment  
     */
    public function findById(int|string $id): Payment
    {
        return $this->model->findOrFail($id);
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
     * Get payment details by order ID.
     *
     * @param int $orderId
     * @return Payment|null
     */
    public function getPaymentByOrderId(int $orderId): ?Payment
    {
        return $this->model->ByOrderId($orderId)->first();
    }

    /**
     * Update payment status.
     *
     * @param int $paymentId
     * @param string $status
     * @return bool
     */
    public function updatePaymentStatus(int $paymentId, string $status): bool
    {
        try {
            $payment = $this->findById($paymentId);

            $updateData = ['status' => $status];

            // Automatically fill paid_at when status is updated to 'paid'
            if ($status === 'paid') {
                $updateData['paid_at'] = now();
            }

            $payment->update($updateData);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get payment status.
     *
     * @param int $paymentId
     * @return string|null
     */
    public function getPaymentStatus(int $paymentId): ?string
    {
        $payment = $this->model->findOrFail($paymentId);
        return $payment ? $payment->status : null;
    }
}
