<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\ProductImage
 *
 * Represents an image belongs to certain product.
 *
 * @property int $id
 * @property int $product_id
 * @property string $title
 * @property string $path
 * @property string $type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 */
class ProductImage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'product_id',
        'path',
        'type',
        'title'
    ];

    /**
     * Get the product for this image
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
    /**
     * Scope to filter product images by product ID.
     */
    public function scopeByProductId($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope to filter product images by multiple product IDs.
     */
    public function scopeByIds($query, array $productIds)
    {
        return $query->whereIn('product_id', $productIds);
    }

    /**
     * Scope to filter product images created from date.
     */
    public function scopeCreatedFrom($query, $date)
    {
        return $query->where('created_at', '>=', $date);
    }

    /**
     * Scope to filter product images created to date.
     */
    public function scopeCreatedTo($query, $date)
    {
        return $query->where('created_at', '<=', $date);
    }

    /**
     * Scope to filter product images created on a specific date.
     */
    public function scopeCreatedOn($query, $date)
    {
        return $query->whereDate('created_at', $date);
    }

    /**
     * Scope to filter product images by type (jpg/png).
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to filter product images by title (partial match).
     */
    public function scopeByTitle($query, string $title)
    {
        return $query->where('title', 'LIKE', "%{$title}%");
    }

    /**
     * Scope to filter product images with multiple criteria.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters Available filters:
     *               - search: Search in path and title fields (partial match)
     *               - product_id: Filter by product ID (exact match)
     *               - product_ids: Filter by multiple product IDs
     *               - type: Filter by image type (jpg/png)
     *               - title: Filter by title (partial match)
     *               - created_from: Filter product images created from date onwards
     *               - created_to: Filter product images created up to date
     *               - created_on: Filter product images created on specific date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, array $filters)
    {
        return $query->when(isset($filters['product_id']), function ($q) use ($filters) {
            return $q->byProductId($filters['product_id']);
        })
            ->when(isset($filters['product_ids']), function ($q) use ($filters) {
                return $q->byProductIds($filters['product_ids']);
            })
            ->when(isset($filters['type']), function ($q) use ($filters) {
                return $q->byType($filters['type']);
            })
            ->when(isset($filters['title']), function ($q) use ($filters) {
                return $q->byTitle($filters['title']);
            })
            ->when(isset($filters['search']), function ($q) use ($filters) {
                return $q->where(function ($subQuery) use ($filters) {
                    $subQuery->where('path', 'LIKE', "%{$filters['search']}%")
                             ->orWhere('title', 'LIKE', "%{$filters['search']}%");
                });
            })
            ->when(isset($filters['created_from']), function ($q) use ($filters) {
                return $q->createdFrom($filters['created_from']);
            })
            ->when(isset($filters['created_to']), function ($q) use ($filters) {
                return $q->createdTo($filters['created_to']);
            })
            ->when(isset($filters['created_on']), function ($q) use ($filters) {
                return $q->createdOn($filters['created_on']);
            });
    }
}
