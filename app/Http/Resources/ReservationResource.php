<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = $request->header('Accept-Language', 'fr');

        return [
            'id' => $this->id,
            'property_id' => $this->property_id,
            'check_in_date' => $this->check_in_date?->toDateString(),
            'check_out_date' => $this->check_out_date?->toDateString(),
            'nights_count' => $this->check_in_date && $this->check_out_date
                ? $this->check_in_date->diffInDays($this->check_out_date)
                : 0,
            'guests_count' => $this->guests_count,
            'total_price_dzd' => $this->total_price_dzd,
            'currency_code' => $this->currency_code,
            'status' => $this->status,
            'special_requests' => $this->special_requests,
            'can_be_cancelled' => $this->canBeCancelled(),
            'is_completed' => $this->isCompleted(),
            'property' => $this->whenLoaded('property', fn() => [
                'id' => $this->property->id,
                'title' => match($locale) {
                    'ar' => $this->property->title_ar,
                    'en' => $this->property->title_en,
                    default => $this->property->title_fr,
                },
                'primary_image_url' => $this->property->primaryImage?->image_path
                    ? asset('storage/' . $this->property->primaryImage->image_path)
                    : null,
                'address' => $this->property->address,
                'wilaya' => [
                    'id' => $this->property->wilaya->id,
                    'name' => match($locale) {
                        'ar' => $this->property->wilaya->name_ar,
                        'en' => $this->property->wilaya->name_en,
                        default => $this->property->wilaya->name_fr,
                    },
                ],
            ]),
            'payment' => $this->whenLoaded('payment', fn() => [
                'id' => $this->payment->id,
                'status' => $this->payment->status,
                'payment_method' => $this->payment->payment_method,
            ]),
            'review' => $this->whenLoaded('review', fn() => ReviewResource::make($this->review)),
            'created_at' => $this->created_at?->toDateTimeString(),
            'cancelled_at' => $this->cancelled_at?->toDateTimeString(),
            'confirmed_at' => $this->confirmed_at?->toDateTimeString(),
        ];
    }
}
