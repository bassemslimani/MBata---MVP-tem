<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'amount_dzd',
        'currency_code',
        'payment_method',
        'status',
        'stripe_payment_id',
        'payment_proof_path',
        'admin_notes',
        'verified_at',
        'verified_by',
    ];

    protected function casts(): array
    {
        return [
            'amount_dzd' => 'decimal:10',
            'verified_at' => 'datetime',
        ];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
