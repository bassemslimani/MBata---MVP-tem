<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'reservation_id',
        'user_id',
        'rating',
        'comment_fr',
        'comment_ar',
        'is_visible',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_visible' => 'boolean',
        ];
    }

    /**
     * Scope for visible reviews only.
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * Scope to filter by property.
     */
    public function scopeByProperty($query, int $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    /**
     * Scope to filter by user.
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
