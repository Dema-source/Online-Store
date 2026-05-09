<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\CartItem
 *
 * Represents a Cart belongs to a Profile.
 *
 * @property int $id
 * @property int $cart_id
 * @property int $product_id
 * @property int $quantity
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 */
class CartItem extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
    ];

    /**
     * Get the cart that owns the cartItem
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Get the product that related to the cartItem
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope to filter cart items by cart ID.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int|string $cartId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCartId($query, int|string $cartId)
    {
        return $query->where('cart_id', $cartId);
    }

    /**
     * Scope to filter cart items by multiple cart IDs.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $cartIds
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCartIds($query, array $cartIds)
    {
        return $query->whereIn('cart_id', $cartIds);
    }

    /**
     * Scope to filter cart items by product ID.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int|string $productId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByProductId($query, int|string $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope to filter cart items by multiple product IDs.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $productIds
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByProductIds($query, array $productIds)
    {
        return $query->whereIn('product_id', $productIds);
    }

    /**
     * Scope to filter cart items by quantity.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $quantity
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByQuantity($query, int $quantity)
    {
        return $query->where('quantity', $quantity);
    }

    /**
     * Scope to filter cart items by minimum quantity.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $minQuantity
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByMinQuantity($query, int $minQuantity)
    {
        return $query->where('quantity', '>=', $minQuantity);
    }

    /**
     * Scope to filter cart items by maximum quantity.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $maxQuantity
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByMaxQuantity($query, int $maxQuantity)
    {
        return $query->where('quantity', '<=', $maxQuantity);
    }

    /**
     * Scope to filter cart items by quantity range.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $minQuantity
     * @param int $maxQuantity
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByQuantityRange($query, int $minQuantity, int $maxQuantity)
    {
        return $query->whereBetween('quantity', [$minQuantity, $maxQuantity]);
    }

    /**
     * Scope to filter cart items with multiple criteria.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters Available filters:
     *               - cart_id: Filter by specific cart ID
     *               - cart_ids: Filter by multiple cart IDs
     *               - product_id: Filter by specific product ID
     *               - product_ids: Filter by multiple product IDs
     *               - quantity: Filter by specific quantity
     *               - min_quantity: Filter by minimum quantity
     *               - max_quantity: Filter by maximum quantity
     *               - quantity_min: Filter by minimum quantity (alias)
     *               - quantity_max: Filter by maximum quantity (alias)
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, array $filters)
    {
        return $query->when(isset($filters['cart_id']), function ($q) use ($filters) {
                return $q->byCartId($filters['cart_id']);
            })
            ->when(isset($filters['cart_ids']), function ($q) use ($filters) {
                return $q->byCartIds($filters['cart_ids']);
            })
            ->when(isset($filters['product_id']), function ($q) use ($filters) {
                return $q->byProductId($filters['product_id']);
            })
            ->when(isset($filters['product_ids']), function ($q) use ($filters) {
                return $q->byProductIds($filters['product_ids']);
            })
            ->when(isset($filters['quantity']), function ($q) use ($filters) {
                return $q->byQuantity($filters['quantity']);
            })
            ->when(isset($filters['min_quantity']), function ($q) use ($filters) {
                return $q->byMinQuantity($filters['min_quantity']);
            })
            ->when(isset($filters['max_quantity']), function ($q) use ($filters) {
                return $q->byMaxQuantity($filters['max_quantity']);
            })
            ->when(isset($filters['quantity_min']), function ($q) use ($filters) {
                return $q->byMinQuantity($filters['quantity_min']);
            })
            ->when(isset($filters['quantity_max']), function ($q) use ($filters) {
                return $q->byMaxQuantity($filters['quantity_max']);
            })
            ->when(isset($filters['quantity_min']) && isset($filters['quantity_max']), function ($q) use ($filters) {
                return $q->byQuantityRange($filters['quantity_min'], $filters['quantity_max']);
            });
    }
}
