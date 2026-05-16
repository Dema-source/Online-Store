<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Favourite
 *
 * Represents an Order belongs to a Profile.
 *
 * @property int $id
 * @property int $user_id
 * @property string $guest_token
 * @property int $product_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 */
class Favourite extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'guest_token',
        'product_id',
    ];

    /**
     * Get the user that owns the Favourite
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product that owns the Favourite
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope to filter favourites by user ID.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter favourites by product ID.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $productId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope to filter favourites by guest token.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $guestToken
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByGuestToken($query, string $guestToken)
    {
        return $query->where('guest_token', $guestToken);
    }

    /**
     * Scope to filter favourites by ID.
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
     * Scope to filter favourites created from specific date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedFrom($query, string $date)
    {
        return $query->where('created_at', '>=', $date);
    }

    /**
     * Scope to filter favourites created until specific date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedTo($query, string $date)
    {
        return $query->where('created_at', '<=', $date);
    }

    /**
     * Scope to get favourites created on specific date.
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
     * Scope to get favourites created in the current month.
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
     * Scope to search favourites by product name.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, string $search)
    {
        return $query->whereHas('product', function ($query) use ($search) {
            return $query->where('name', 'like', "%{$search}%");
        });
    }

    /**
     * Scope to apply multiple filters to favourites.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters Available filters:
     *               - search: Search in product names
     *               - user_id: Filter by user ID
     *               - product_id: Filter by product ID
     *               - guest_token: Filter by guest token
     *               - created_from: Filter favourites created after date
     *               - created_to: Filter favourites created before date
     *               - created_on: Filter favourites created on specific date
     *               - created_this_month: Filter favourites created this month
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByFilter($query, array $filters)
    {
        return $query->when(isset($filters['search']), function ($q) use ($filters) {
            return $q->search($filters['search']);
        })
            ->when(isset($filters['user_id']), function ($q) use ($filters) {
                return $q->byUser($filters['user_id']);
            })
            ->when(isset($filters['product_id']), function ($q) use ($filters) {
                return $q->byProduct($filters['product_id']);
            })
            ->when(isset($filters['guest_token']), function ($q) use ($filters) {
                return $q->byGuestToken($filters['guest_token']);
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
            });
    }
}
