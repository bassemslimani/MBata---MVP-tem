<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PropertyCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_fr',
        'name_ar',
        'name_en',
        'description_fr',
        'description_ar',
        'description_en',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }
}
