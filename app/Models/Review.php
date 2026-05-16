<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App/Model/Review
 * presents a 
 * 
 * @property int $id
 * @property int $profile_id
 * @property int $product_id
 * @property enum $rating
 * @property string $comment
 * @property Carbon|null $created_at	
 * @property Carbon|null $updated_at	
 * 
 */
class Review extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'profile_id',
        'product_id',
        'rating',
        'comment',
    ];

    /**
     * Get the user that owns the Profile
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Get the user that owns the Profile
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope to filter reviews by profile ID.
     */
    public function scopeByProfileId($query, int $profileId)
    {
        return $query->where('profile_id', $profileId);
    }

    /**
     * Scope to filter reviews by product ID.
     */
    public function scopeByProductId($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope to filter reviews by rating.
     */
    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Scope to filter reviews created from date.
     */
    public function scopeCreatedFrom($query, $date)
    {
        return $query->where('created_at', '>=', $date);
    }

    /**
     * Scope to filter reviews created to date.
     */
    public function scopeCreatedTo($query, $date)
    {
        return $query->where('created_at', '<=', $date);
    }

    /**
     * Scope to filter reviews created on a specific date.
     */
    public function scopeCreatedOn($query, $date)
    {
        return $query->whereDate('created_at', $date);
    }

    /**
     * Scope to filter reviews by ID.
     */
    public function scopeById($query, int $id)
    {
        return $query->where('id', $id);
    }

    /**
     * Scope to search reviews across multiple fields.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search The search term.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($subQuery) use ($search) {
            $subQuery->where('comment', 'like', "%{$search}%");
        });
    }

    /**
     * Scope to filter reviews with multiple criteria.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters Available filters:
     *               - profile_id: Filter by profile ID (exact match)
     *               - product_id: Filter by product ID (exact match)
     *               - rating: Filter by rating (exact match)
     *               - comment: Filter by comment (partial match)
     *               - created_from: Filter reviews created from date onwards
     *               - created_to: Filter reviews created up to date
     *               - created_on: Filter reviews created on specific date
     *               - search: Search in comment field (partial match)
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, array $filters)
    {
        return $query->when(isset($filters['profile_id']), function ($q) use ($filters) {
            return $q->byProfileId($filters['profile_id']);
        })
            ->when(isset($filters['product_id']), function ($q) use ($filters) {
                return $q->byProductId($filters['product_id']);
            })
            ->when(isset($filters['rating']), function ($q) use ($filters) {
                return $q->byRating($filters['rating']);
            })
            ->when(isset($filters['comment']), function ($q) use ($filters) {
                return $q->byComment($filters['comment']);
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
