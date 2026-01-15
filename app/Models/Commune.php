<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Commune extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'wilaya_id',
        'code',
        'name_fr',
        'name_ar',
        'name_en',
        'postal_code',
    ];

    /**
     * Get the wilaya that owns the commune.
     */
    public function wilaya(): BelongsTo
    {
        return $this->belongsTo(Wilaya::class);
    }

    /**
     * Get the properties for the commune.
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }
}
