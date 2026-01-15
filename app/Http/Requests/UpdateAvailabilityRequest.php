<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class UpdateAvailabilityRequest extends FormRequest
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
            'start_date' => ['sometimes', 'date', 'after:today'],
            'end_date' => ['sometimes', 'date', 'after:start_date'],
            'is_available' => ['boolean'],
            'price_override_dzd' => ['nullable', 'decimal:0,2', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
