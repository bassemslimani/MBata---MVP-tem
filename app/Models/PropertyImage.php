<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'image_path',
        'is_primary',
        'order',
        'alt_text_fr',
        'alt_text_ar',
        'alt_text_en',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'order' => 'integer',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
