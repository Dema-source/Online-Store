<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App/Model/User
 * presents a registered user in the application.
 * Users can brawse products, order, book and review.
 * 
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token	
 * @property Carbon|null $created_at	
 * @property Carbon|null $updated_at	
 * 
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the profile associated with the user.
     *
     * Relationship: One-to-One.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Scope to filter users created on a specific date.
     */
    public function scopeCreatedOn($query, $date)
    {
        return $query->whereDate('created_at', $date);
    }

    /**
     * Scope to filter users created from a specific date onwards.
     */
    public function scopeCreatedFrom($query, $date)
    {
        return $query->where('created_at', '>=', $date);
    }

    /**
     * Scope to filter users created up to a specific date.
     */
    public function scopeCreatedTo($query, $date)
    {
        return $query->where('created_at', '<=', $date);
    }

    /**
     * Scope to search users by name (partial match).
     */
    public function scopeSearchByName($query, string $name)
    {
        return $query->where('name', 'like', "%{$name}%");
    }

    /**
     * Scope to search users by email (partial match).
     */
    public function scopeSearchByEmail($query, string $email)
    {
        return $query->where('email', 'like', "%{$email}%");
    }
    /**
     * Scope users by name (partial match).
     */
    public function scopeByName($query, string $name)
    {
        return $query->where('name', $name);
    }

    /**
     * Scope users by email (partial match).
     */
    public function scopeByEmail($query, string $email)
    {
        return $query->where('email', $email);
    }

    /**
     * Scope to find user by ID.
     */
    public function scopeById($query, int $id)
    {
        return $query->where('id', $id);
    }

    /**
     * Scope to find users with verified email.
     */
    public function scopeEmailVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    /**
     * Scope to find users with unverified email.
     */
    public function scopeEmailUnverified($query)
    {
        return $query->whereNull('email_verified_at');
    }

    /**
     * Scope to filter users by multiple criteria.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters Available filters:
     *               - name: Filter by name (partial match)
     *               - email: Filter by email (partial match)
     *               - created_on: Filter by creation date (exact match)
     *               - created_from: Filter by creation date (from date onwards)
     *               - created_to: Filter by creation date (up to date)
     *               - search: Search in name and email fields (partial match)
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, array $filters)
    {
        return $query->when(isset($filters['name']), function ($q) use ($filters) {
                    return $q->where('name', 'like', "%{$filters['name']}%");
                })
                ->when(isset($filters['email']), function ($q) use ($filters) {
                    return $q->where('email', 'like', "%{$filters['email']}%");
                })
                ->when(isset($filters['created_on']), function ($q) use ($filters) {
                    return $q->whereDate('created_at', $filters['created_on']);
                })
                ->when(isset($filters['created_from']), function ($q) use ($filters) {
                    return $q->where('created_at', '>=', $filters['created_from']);
                })
                ->when(isset($filters['created_to']), function ($q) use ($filters) {
                    return $q->where('created_at', '<=', $filters['created_to']);
                })
                ->when(isset($filters['search']), function ($q) use ($filters) {
                    return $q->where(function ($subQuery) use ($filters) {
                        $subQuery->where('name', 'like', "%{$filters['search']}%")
                                 ->orWhere('email', 'like', "%{$filters['search']}%");
                    });
                });
    }
}
