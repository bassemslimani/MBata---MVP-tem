<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wilaya extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'name_fr',
        'name_ar',
        'name_en',
        'longitude',
        'latitude',
    ];

    /**
     * Get the communes for the wilaya.
     */
    public function communes(): HasMany
    {
        return $this->hasMany(Commune::class);
    }

    /**
     * Get the properties for the wilaya.
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }
}
