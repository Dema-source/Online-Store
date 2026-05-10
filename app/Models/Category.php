<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

/**
 * App\Models\Category
 *
 * Represents a category contain products.
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 */
class Category extends Model
{
    use HasTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Fields to be translated.
     * @var array
     */
    public array $translatable = [
        'name'
    ];
    
    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::deleting(function ($category) {
            // Delete product-category relationships
            $category->products()->detach();
        });
    }

    /**
     * The products that belong to the Category
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_category');
    }

        /**
     * Scope to search categories by name.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search The search term.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where('name', 'like', "%{$search}%");
    }

    /**
     * Scope to filter categories by multiple IDs.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $ids Array of category IDs.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByIds($query, array $ids)
    {
        return $query->whereIn('id', $ids);
    }

    /**
     * Scope to filter categories created from date.
     */
    public function scopeCreatedFrom($query, $date)
    {
        return $query->where('created_at', '>=', $date);
    }

    /**
     * Scope to filter categories created to date.
     */
    public function scopeCreatedTo($query, $date)
    {
        return $query->where('created_at', '<=', $date);
    }

    /**
     * Scope to filter categories created on a specific date.
     */
    public function scopeCreatedOn($query, $date)
    {
        return $query->whereDate('created_at', $date);
    }

    /**
     * Scope to filter categories with multiple criteria.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters Available filters:
     *               - search: Search in name field (partial match)
     *               - created_from: Filter categories created from date onwards
     *               - created_to: Filter categories created up to date
     *               - created_on: Filter categories created on specific date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, array $filters)
    {
        return $query->when(isset($filters['search']), function ($q) use ($filters) {
                    return $q->search($filters['search']);
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
