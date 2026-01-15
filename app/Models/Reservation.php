<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'client_user_id',
        'check_in_date',
        'check_out_date',
        'guests_count',
        'total_price_dzd',
        'currency_code',
        'status',
        'special_requests',
        'cancelled_at',
        'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'check_in_date' => 'date',
            'check_out_date' => 'date',
            'total_price_dzd' => 'decimal:2',
            'cancelled_at' => 'datetime',
            'confirmed_at' => 'datetime',
        ];
    }

    /**
     * Scope to filter by status.
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for pending reservations.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for confirmed reservations.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope for active reservations (not cancelled).
     */
    public function scopeActive($query)
    {
        return $query->whereNot('status', 'cancelled');
    }

    /**
     * Scope to filter by client.
     */
    public function scopeByClient($query, int $userId)
    {
        return $query->where('client_user_id', $userId);
    }

    /**
     * Scope to filter by property.
     */
    public function scopeByProperty($query, int $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    /**
     * Scope for overlapping date range.
     */
    public function scopeOverlapping($query, string $checkIn, string $checkOut)
    {
        return $query->where(function ($q) use ($checkIn, $checkOut) {
            $q->where(function ($q2) use ($checkIn, $checkOut) {
                $q2->where('check_in_date', '<=', $checkOut)
                    ->where('check_out_date', '>=', $checkIn);
            });
        })->whereIn('status', ['pending', 'confirmed']);
    }

    /**
     * Check if reservation can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed'])
            && $this->check_in_date->isFuture();
    }

    /**
     * Check if reservation is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed'
            || $this->check_out_date->isPast();
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_user_id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}
