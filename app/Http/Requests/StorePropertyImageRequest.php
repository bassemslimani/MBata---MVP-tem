<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class StorePropertyImageRequest extends FormRequest
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
            'image' => ['required', 'image', 'max:5120'], // Max 5MB
            'is_primary' => ['boolean'],
            'order' => ['integer', 'min:0'],
            'alt_text_fr' => ['nullable', 'string', 'max:255'],
            'alt_text_ar' => ['nullable', 'string', 'max:255'],
            'alt_text_en' => ['nullable', 'string', 'max:255'],
        ];
    }
}
