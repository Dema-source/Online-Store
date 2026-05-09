<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

/**
 * App\Models\Product
 *
 * Represents a product could belongs to many categories.
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $brand
 * @property float $price
 * @property int $stock
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 */
class Product extends Model
{
    use HasTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'brand',
        'price',
        'stock'
    ];

    /**
     * Fields to be translated.
     * @var array
     */
    public array $translatable = [
        'name',
        'description'
    ];

    /**
     * The categories that could contain the product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_category');
    }

    /**
     * Get all of the images for the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * The carts that cantain the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function cart_items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Scope to search products across multiple fields.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search The search term.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where('name', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%")
            ->orWhere('brand', 'like', "%{$search}%");
    }

    /**
     * Scope to filter products by name.
     */
    public function scopeByName($query, string $name)
    {
        return $query->where('name', 'like', "%{$name}%");
    }

    /**
     * Scope to filter products by description.
     */
    public function scopeByDescription($query, string $description)
    {
        return $query->where('description', 'like', "%{$description}%");
    }

    /**
     * Scope to filter products by brand.
     */
    public function scopeByBrand($query, string $brand)
    {
        return $query->where('brand', $brand);
    }

    /**
     * Scope to filter products by minimum price.
     */
    public function scopePriceFrom($query, float $price)
    {
        return $query->where('price', '>=', $price);
    }

    /**
     * Scope to filter products by maximum price.
     */
    public function scopePriceTo($query, float $price)
    {
        return $query->where('price', '<=', $price);
    }

    /**
     * Scope to filter products by minimum stock.
     */
    public function scopeStockFrom($query, int $stock)
    {
        return $query->where('stock', '>=', $stock);
    }

    /**
     * Scope to filter products by maximum stock.
     */
    public function scopeStockTo($query, int $stock)
    {
        return $query->where('stock', '<=', $stock);
    }

    /**
     * Scope to filter products created from date.
     */
    public function scopeCreatedFrom($query, $date)
    {
        return $query->where('created_at', '>=', $date);
    }

    /**
     * Scope to filter products created to date.
     */
    public function scopeCreatedTo($query, $date)
    {
        return $query->where('created_at', '<=', $date);
    }

    /**
     * Scope to filter products created on a specific date.
     */
    public function scopeCreatedOn($query, $date)
    {
        return $query->whereDate('created_at', $date);
    }

    /**
     * Scope to filter products by multiple IDs.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $ids Array of product IDs.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByIds($query, array $ids)
    {
        return $query->whereIn('id', $ids);
    }

    /**
     * Scope to filter products with multiple criteria.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters Available filters:
     *               - search: Search in name, description, brand fields (partial match)
     *               - brand: Filter by brand (exact match)
     *               - price_from: Filter products with minimum price
     *               - price_to: Filter products with maximum price
     *               - stock_from: Filter products with minimum stock
     *               - stock_to: Filter products with maximum stock
     *               - created_from: Filter products created from date onwards
     *               - created_to: Filter products created up to date
     *               - created_on: Filter products created on specific date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, array $filters)
    {
        return $query->when(isset($filters['search']), function ($q) use ($filters) {
            return $q->search($filters['search']);
        })
            ->when(isset($filters['brand']), function ($q) use ($filters) {
                return $q->byBrand($filters['brand']);
            })
            ->when(isset($filters['price_from']), function ($q) use ($filters) {
                return $q->priceFrom($filters['price_from']);
            })
            ->when(isset($filters['price_to']), function ($q) use ($filters) {
                return $q->priceTo($filters['price_to']);
            })
            ->when(isset($filters['stock_from']), function ($q) use ($filters) {
                return $q->stockFrom($filters['stock_from']);
            })
            ->when(isset($filters['stock_to']), function ($q) use ($filters) {
                return $q->stockTo($filters['stock_to']);
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
