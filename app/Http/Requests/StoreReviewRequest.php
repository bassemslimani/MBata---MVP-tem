<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
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
            'reservation_id' => ['required', 'exists:reservations,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment_fr' => ['nullable', 'string', 'max:2000'],
            'comment_ar' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
