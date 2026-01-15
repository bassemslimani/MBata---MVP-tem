<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchPropertiesRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'wilaya_id' => ['nullable', 'exists:wilayas,id'],
            'commune_id' => ['nullable', 'exists:communes,id'],
            'property_type_id' => ['nullable', 'exists:property_types,id'],
            'property_category_id' => ['nullable', 'exists:property_categories,id'],
            'check_in' => ['nullable', 'date', 'after:today'],
            'check_out' => ['nullable', 'date', 'after:check_in'],
            'guests' => ['nullable', 'integer', 'min:1', 'max:50'],
            'price_min' => ['nullable', 'decimal:0,2', 'min:0'],
            'price_max' => ['nullable', 'decimal:0,2', 'min:0', 'gte:price_min'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['exists:amenities,id'],
            'sort' => ['nullable', 'in:price_asc,price_desc,rating_desc,created_desc,views_desc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
