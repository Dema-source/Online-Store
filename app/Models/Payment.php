<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Payment
 *
 * Represents a Cart belongs to a Profile.
 *
 * @property int $id
 * @property int $profile_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 */
class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'order_id',
        'payment_method',
        'status',
        'transaction_id',
        'amount',
        'paid_at',
    ];
    
    /**
     * Get the order that owns the Payment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Scope a query to only include payment by ID.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeById($query, int $id)
    {
        return $query->where('id', $id);
    }

    /**
     * Scope a query to only include payment by order ID.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $orderId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByOrderId($query, int $orderId)
    {
        return $query->where('order_id', $orderId);
    }

    /**
     * Scope to get payments created on a specific date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedOn($query, string $date)
    {
        return $query->whereDate('created_at', $date);
    }

    /**
     * Scope to get payments created from a specific date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedFrom($query, string $date)
    {
        return $query->whereDate('created_at', '>=', $date);
    }

    /**
     * Scope to get payments created until a specific date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedTo($query, string $date)
    {
        return $query->whereDate('created_at', '<=', $date);
    }

    /**
     * Scope to get payments created in the current month.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }

    /**
     * Scope to filter payments by status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter payments by minimum amount.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param float $amount
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMinAmount($query, float $amount)
    {
        return $query->where('amount', '>=', $amount);
    }

    /**
     * Scope to filter payments by maximum amount.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param float $amount
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMaxAmount($query, float $amount)
    {
        return $query->where('amount', '<=', $amount);
    }

    /**
     * Scope to filter payments by paid_at date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePaidOn($query, string $date)
    {
        return $query->whereDate('paid_at', $date);
    }

    /**
     * Scope to filter payments by paid_at from date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePaidFrom($query, string $date)
    {
        return $query->whereDate('paid_at', '>=', $date);
    }

    /**
     * Scope to filter payments by paid_at to date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePaidTo($query, string $date)
    {
        return $query->whereDate('paid_at', '<=', $date);
    }

    /**
     * Scope to search payments by various fields.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($query) use ($search) {
            return $query->where('transaction_id', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%");
        });
    }

    /**
     * Scope to apply multiple filters to payments.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByFilter($query, array $filters)
    {
        return $query
            ->when(isset($filters['search']), function ($q) use ($filters) {
                return $q->search($filters['search']);
            })
            ->when(isset($filters['status']), function ($q) use ($filters) {
                return $q->byStatus($filters['status']);
            })
            ->when(isset($filters['min_amount']), function ($q) use ($filters) {
                return $q->minAmount($filters['min_amount']);
            })
            ->when(isset($filters['max_amount']), function ($q) use ($filters) {
                return $q->maxAmount($filters['max_amount']);
            })
            ->when(isset($filters['created_on']), function ($q) use ($filters) {
                return $q->createdOn($filters['created_on']);
            })
            ->when(isset($filters['created_from']), function ($q) use ($filters) {
                return $q->createdFrom($filters['created_from']);
            })
            ->when(isset($filters['created_to']), function ($q) use ($filters) {
                return $q->createdTo($filters['created_to']);
            })
            ->when(isset($filters['created_this_month']), function ($q) use ($filters) {
                return $q->createdThisMonth();
            })
            ->when(isset($filters['paid_on']), function ($q) use ($filters) {
                return $q->paidOn($filters['paid_on']);
            })
            ->when(isset($filters['paid_from']), function ($q) use ($filters) {
                return $q->paidFrom($filters['paid_from']);
            })
            ->when(isset($filters['paid_to']), function ($q) use ($filters) {
                return $q->paidTo($filters['paid_to']);
            });
    }

}
