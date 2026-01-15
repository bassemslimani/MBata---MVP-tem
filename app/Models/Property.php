<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'wilaya_id',
        'commune_id',
        'property_type_id',
        'property_category_id',
        'title_fr',
        'title_ar',
        'title_en',
        'description_fr',
        'description_ar',
        'description_en',
        'address',
        'latitude',
        'longitude',
        'price_per_night_dzd',
        'surface_area',
        'rooms',
        'bedrooms',
        'bathrooms',
        'max_guests',
        'is_active',
        'is_verified',
        'views_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'price_per_night_dzd' => 'decimal:2',
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
            'views_count' => 'integer',
        ];
    }

    /**
     * Scope to only include active properties.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to only include verified properties.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope to filter by owner.
     */
    public function scopeByOwner($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by favorites for a user.
     */
    public function scopeFavoritedBy($query, int $userId)
    {
        return $query->whereHas('favorites', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    /**
     * Scope for available properties within date range.
     */
    public function scopeAvailableBetween($query, string $checkIn, string $checkOut)
    {
        return $query->whereDoesntHave('reservations', function ($q) use ($checkIn, $checkOut) {
            $q->overlapping($checkIn, $checkOut);
        });
    }

    /**
     * Scope to search by amenities.
     */
    public function scopeWithAmenities($query, array $amenityIds)
    {
        return $query->whereHas('amenities', function ($q) use ($amenityIds) {
            $q->whereIn('amenities.id', $amenityIds);
        });
    }

    /**
     * Get the primary image for the property.
     */
    public function primaryImage()
    {
        return $this->hasOne(PropertyImage::class)->where('is_primary', true);
    }

    /**
     * Get the favorites for the property.
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Get visible reviews for the property.
     */
    public function visibleReviews()
    {
        return $this->hasMany(Review::class)->visible();
    }

    /**
     * Get average rating for the property.
     */
    public function averageRating()
    {
        return $this->visibleReviews()->avg('rating') ?? 0;
    }

    /**
     * Count of visible reviews.
     */
    public function reviewsCount()
    {
        return $this->visibleReviews()->count();
    }

    /**
     * Get the user (owner) that owns the property.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the wilaya that the property belongs to.
     */
    public function wilaya(): BelongsTo
    {
        return $this->belongsTo(Wilaya::class);
    }

    /**
     * Get the commune that the property belongs to.
     */
    public function commune(): BelongsTo
    {
        return $this->belongsTo(Commune::class);
    }

    /**
     * Get the property type for the property.
     */
    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class);
    }

    /**
     * Get the property category for the property.
     */
    public function propertyCategory(): BelongsTo
    {
        return $this->belongsTo(PropertyCategory::class);
    }

    /**
     * The amenities that belong to the property.
     */
    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class);
    }

    /**
     * Get the reservations for the property.
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Get the reviews for the property.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the images for the property.
     */
    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class);
    }

    /**
     * Get the availabilities for the property.
     */
    public function availabilities(): HasMany
    {
        return $this->hasMany(Availability::class);
    }
}
