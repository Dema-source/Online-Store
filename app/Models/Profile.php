<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\Profile
 *
 * Represents a profile belongs to a User.
 *
 * @property int $id
 * @property int $user_id
 * @property string $phone
 * @property string $address
 * @property date $date_of_birth
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 */
class Profile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'phone',
        'address',
        'date_of_birth',
    ];

    /**
     * Get the user that owns the Profile
     *
     * Relationship: One-to-One.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    /**
     * Get the cart associated with the user.
     *
     * Relationship: One-to-One.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    /**
     * Get all of the orders for the Profile
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get all of the reviews for the Profile
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Scope to filter profiles by user ID.
     */
    public function scopeByUserId($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter profiles by phone.
     */
    public function scopeByPhone($query, string $phone)
    {
        return $query->where('phone', 'like', "%{$phone}%");
    }

    /**
     * Scope to filter profiles by address.
     */
    public function scopeByAddress($query, string $address)
    {
        return $query->where('address', 'like', "%{$address}%");
    }

    /**
     * Scope to filter profiles by date of birth.
     */
    public function scopeByDateOfBirth($query, $date)
    {
        return $query->whereDate('date_of_birth', $date);
    }

    /**
     * Scope to filter profiles created from date.
     */
    public function scopeCreatedFrom($query, $date)
    {
        return $query->where('created_at', '>=', $date);
    }

    /**
     * Scope to filter profiles created to date.
     */
    public function scopeCreatedTo($query, $date)
    {
        return $query->where('created_at', '<=', $date);
    }

    /**
     * Scope to filter profiles created on a specific date.
     */
    public function scopeCreatedOn($query, $date)
    {
        return $query->whereDate('created_at', $date);
    }

    /**
     * Scope to filter profiles by ID.
     */
    public function scopeById($query, int $id)
    {
        return $query->where('id', $id);
    }

    /**
     * Scope to search profiles across multiple fields.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search The search term.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($subQuery) use ($search) {
            $subQuery->where('phone', 'like', "%{$search}%")
                ->orWhere('address', 'like', "%{$search}%");
        });
    }

    /*
     * Scope to filter profiles created on a specific date.
     *
     * Scope to filter profiles with multiple criteria.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters Available filters:
     *               - user_id: Filter by user ID (exact match)
     *               - phone: Filter by phone number (partial match)
     *               - address: Filter by address (partial match)
     *               - date_of_birth: Filter by date of birth (exact match)
     *               - created_from: Filter profiles created from date onwards
     *               - created_to: Filter profiles created up to date
     *               - created_on: Filter profiles created on specific date
     *               - search: Search in phone and address fields (partial match)
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, array $filters)
    {
        return $query->when(isset($filters['user_id']), function ($q) use ($filters) {
            return $q->byUserId($filters['user_id']);
        })
            ->when(isset($filters['phone']), function ($q) use ($filters) {
                return $q->byPhone($filters['phone']);
            })
            ->when(isset($filters['address']), function ($q) use ($filters) {
                return $q->byAddress($filters['address']);
            })
            ->when(isset($filters['date_of_birth']), function ($q) use ($filters) {
                return $q->byDateOfBirth($filters['date_of_birth']);
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
