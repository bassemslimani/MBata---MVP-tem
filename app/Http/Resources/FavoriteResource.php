<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
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
            'property' => $this->whenLoaded('property', fn() => [
                'id' => $this->property->id,
                'title' => match($locale) {
                    'ar' => $this->property->title_ar,
                    'en' => $this->property->title_en,
                    default => $this->property->title_fr,
                },
                'price_per_night_dzd' => $this->property->price_per_night_dzd,
                'bedrooms' => $this->property->bedrooms,
                'max_guests' => $this->property->max_guests,
                'primary_image_url' => $this->property->primaryImage?->image_path
                    ? asset('storage/' . $this->property->primaryImage->image_path)
                    : null,
                'average_rating' => round($this->property->visibleReviews->avg('rating') ?? 0, 1),
                'reviews_count' => $this->property->visibleReviews->count(),
                'wilaya' => [
                    'id' => $this->property->wilaya->id,
                    'name' => match($locale) {
                        'ar' => $this->property->wilaya->name_ar,
                        'en' => $this->property->wilaya->name_en,
                        default => $this->property->wilaya->name_fr,
                    },
                ],
            ]),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
