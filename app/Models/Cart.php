<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Cart
 *
 * Represents a Cart belongs to a Profile.
 *
 * @property int $id
 * @property int $profile_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 */
class Cart extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'profile_id',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::deleting(function ($cart) {
            // Delete cart items when cart is deleted
            $cart->cart_items()->delete();
        });
    }

    /**
     * Get the profile that owns the Cart
     *
     * Relationship: One-to-One.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * The products that belong to the Cart
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function cart_items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Scope to search carts across multiple fields.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search The search term.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, string $search)
    {
        return $query->whereHas('profile', function ($subQuery) use ($search) {
            $subQuery->where('phone', 'like', "%{$search}%")
                ->orWhere('address', 'like', "%{$search}%");
        });
    }

    /**
     * Scope to filter carts by profile ID.
     */
    public function scopeByProfileId($query, int $profileId)
    {
        return $query->where('profile_id', $profileId);
    }

    /**
     * Scope to filter carts created from date.
     */
    public function scopeCreatedFrom($query, $date)
    {
        return $query->where('created_at', '>=', $date);
    }

    /**
     * Scope to filter carts created to date.
     */
    public function scopeCreatedTo($query, $date)
    {
        return $query->where('created_at', '<=', $date);
    }

    /**
     * Scope to filter carts created on a specific date.
     */
    public function scopeCreatedOn($query, $date)
    {
        return $query->whereDate('created_at', $date);
    }

    /**
     * Scope to filter carts by ID.
     */
    public function scopeById($query, int $id)
    {
        return $query->where('id', $id);
    }

    /**
     * Scope to filter carts by multiple IDs.
     */
    public function scopeByIds($query, array $ids)
    {
        return $query->whereIn('id', $ids);
    }

    /**
     * Scope to filter carts with multiple criteria.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters Available filters:
     *               - user_id: Filter by user ID (exact match)
     *               - created_from: Filter carts created from date onwards
     *               - created_to: Filter carts created up to date
     *               - created_on: Filter carts created on specific date
     *               - search: Search in user name and email fields (partial match)
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, array $filters)
    {
        return $query->when(isset($filters['user_id']), function ($q) use ($filters) {
            return $q->byUserId($filters['user_id']);
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
            ->when(isset($filters['search']), function ($q) use ($filters) {
                return $q->search($filters['search']);
            });
    }
}
