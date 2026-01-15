<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class StoreReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isClient();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'property_id' => ['required', 'exists:properties,id'],
            'check_in_date' => ['required', 'date', 'after:today'],
            'check_out_date' => ['required', 'date', 'after:check_in_date'],
            'guests_count' => ['required', 'integer', 'min:1'],
            'special_requests' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'check_in_date.after' => 'Check-in date must be after today.',
            'check_out_date.after' => 'Check-out date must be after check-in date.',
        ];
    }
}
