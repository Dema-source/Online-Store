<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\Order
 *
 * Represents an Order belongs to a Profile.
 *
 * @property int $id
 * @property int $profile_id
 * @property string $status
 * @property float $total_price
 * @property string|null $shipping_address
 * @property string|null $phone
 * @property date|null $order_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 */
class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'profile_id',
        'status',
        'total_price',
        'shipping_address',
        'phone',
        'order_date',
    ];

    /**
     * Get the profile that owns the order
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Get all of the order_items for the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function order_items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the payment associated with the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Scope to filter orders by status.
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
     * Scope to filter orders by profile ID.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $profileId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByProfile($query, int $profileId)
    {
        return $query->where('profile_id', $profileId);
    }

    /**
     * Scope to filter orders by ID.
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
     * Scope to get orders with total price above minimum.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param float $amount
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMinTotal($query, float $amount)
    {
        return $query->where('total_price', '>=', $amount);
    }

    /**
     * Scope to get orders with total price below maximum.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param float $amount
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMaxTotal($query, float $amount)
    {
        return $query->where('total_price', '<=', $amount);
    }

    /**
     * Scope to filter orders created from specific date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedFrom($query, string $date)
    {
        return $query->where('order_date', '>=', $date);
    }

    /**
     * Scope to filter orders created until specific date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedTo($query, string $date)
    {
        return $query->where('order_date', '<=', $date);
    }

    /**
     * Scope to get orders created on specific date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedOn($query, string $date)
    {
        return $query->whereDate('order_date', $date);
    }

    /**
     * Scope to filter orders by order date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByOrderDate($query, string $date)
    {
        return $query->whereDate('order_date', $date);
    }

    /**
     * Scope to filter orders from specific order date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderDateFrom($query, string $date)
    {
        return $query->where('order_date', '>=', $date);
    }

    /**
     * Scope to filter orders until specific order date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderDateTo($query, string $date)
    {
        return $query->where('order_date', '<=', $date);
    }

    /**
     * Scope to get orders created in the current month.
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
     * Scope to search orders by order number or address.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($query) use ($search) {
            return $query->where('shipping_address', 'like', "%{$search}%")
                ->orWhereHas('order_items', function ($query) use ($search) {
                    return $query->whereHas('product', function ($query) use ($search) {
                        return $query->where('name', 'like', "%{$search}%");
                    });
                });
        });
    }

    /**
     * Scope to apply multiple filters to orders.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters Available filters:
     *               - search: Search in shipping_address, product names, customer email
     *               - status: Filter by order status
     *               - profile_id: Filter by profile ID
     *               - min_total: Filter orders with minimum total
     *               - max_total: Filter orders with maximum total
     *               - created_from: Filter orders created after date (uses order_date)
     *               - created_to: Filter orders created before date (uses order_date)
     *               - created_on: Filter orders created on specific date (uses order_date)
     *               - created_this_month: Filter orders created this month
     *               - order_date: Filter orders by specific order date
     *               - order_date_from: Filter orders from specific order date
     *               - order_date_to: Filter orders until specific order date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByFilter($query, array $filters)
    {
        return $query->when(isset($filters['search']), function ($q) use ($filters) {
            return $q->search($filters['search']);
        })
            ->when(isset($filters['status']), function ($q) use ($filters) {
                return $q->byStatus($filters['status']);
            })
            ->when(isset($filters['profile_id']), function ($q) use ($filters) {
                return $q->byProfile($filters['profile_id']);
            })
            ->when(isset($filters['created_from']), function ($q) use ($filters) {
                return $q->createdFrom($filters['created_from']);
            })
            ->when(isset($filters['created_to']), function ($q) use ($filters) {
                return $q->createdTo($filters['created_to']);
            })
            ->when(isset($filters['created_on']), function ($q) use ($filters) {
                return $q->createdOn($filters['created_on']);
            })
            ->when(isset($filters['created_this_month']), function ($q) use ($filters) {
                return $q->createdThisMonth();
            })
            ->when(isset($filters['order_date']), function ($q) use ($filters) {
                return $q->byOrderDate($filters['order_date']);
            })
            ->when(isset($filters['order_date_from']), function ($q) use ($filters) {
                return $q->orderDateFrom($filters['order_date_from']);
            })
            ->when(isset($filters['order_date_to']), function ($q) use ($filters) {
                return $q->orderDateTo($filters['order_date_to']);
            })
            ->when(isset($filters['min_total']), function ($q) use ($filters) {
                return $q->minTotal($filters['min_total']);
            })
            ->when(isset($filters['max_total']), function ($q) use ($filters) {
                return $q->maxTotal($filters['max_total']);
            });
    }
}
