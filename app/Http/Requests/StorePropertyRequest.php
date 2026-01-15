<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class StorePropertyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'wilaya_id' => ['required', 'exists:wilayas,id'],
            'commune_id' => ['required', 'exists:communes,id'],
            'property_type_id' => ['required', 'exists:property_types,id'],
            'property_category_id' => ['required', 'exists:property_categories,id'],
            'title_fr' => ['required', 'string', 'max:255'],
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['required', 'string', 'max:255'],
            'description_fr' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:500'],
            'latitude' => ['nullable', 'decimal:7,10'],
            'longitude' => ['nullable', 'decimal:7,10'],
            'price_per_night_dzd' => ['required', 'decimal:0,2', 'min:0'],
            'surface_area' => ['nullable', 'integer', 'min:1'],
            'rooms' => ['nullable', 'integer', 'min:1'],
            'bedrooms' => ['nullable', 'integer', 'min:1'],
            'bathrooms' => ['nullable', 'integer', 'min:1'],
            'max_guests' => ['required', 'integer', 'min:1'],
            'is_active' => ['boolean'],
            'amenity_ids' => ['array'],
            'amenity_ids.*' => ['exists:amenities,id'],
        ];
    }
}
