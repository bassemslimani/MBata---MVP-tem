<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertySearchResource extends JsonResource
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
            'title' => match($locale) {
                'ar' => $this->title_ar,
                'en' => $this->title_en,
                default => $this->title_fr,
            },
            'description' => match($locale) {
                'ar' => $this->description_ar,
                'en' => $this->description_en,
                default => $this->description_fr,
            },
            'address' => $this->address,
            'latitude' => $this->latitude?->toFloat(),
            'longitude' => $this->longitude?->toFloat(),
            'price_per_night_dzd' => $this->price_per_night_dzd,
            'surface_area' => $this->surface_area,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'max_guests' => $this->max_guests,
            'is_verified' => $this->is_verified,
            'views_count' => $this->views_count,
            'primary_image' => $this->whenLoaded('primaryImage', fn() => PropertyImageResource::make($this->primaryImage)),
            'primary_image_url' => $this->whenLoaded('primaryImage', fn() =>
                $this->primaryImage?->image_path ? asset('storage/' . $this->primaryImage->image_path) : null
            ),
            'average_rating' => $this->whenLoaded('visibleReviews', fn() => round($this->visibleReviews->avg('rating') ?? 0, 1)),
            'reviews_count' => $this->whenLoaded('visibleReviews', fn() => $this->visibleReviews->count()),
            'property_type' => $this->whenLoaded('propertyType', fn() => [
                'id' => $this->propertyType->id,
                'name' => match($locale) {
                    'ar' => $this->propertyType->name_ar,
                    'en' => $this->propertyType->name_en,
                    default => $this->propertyType->name_fr,
                },
            ]),
            'wilaya' => $this->whenLoaded('wilaya', fn() => [
                'id' => $this->wilaya->id,
                'code' => $this->wilaya->code,
                'name' => match($locale) {
                    'ar' => $this->wilaya->name_ar,
                    'en' => $this->wilaya->name_en,
                    default => $this->wilaya->name_fr,
                },
            ]),
            'commune' => $this->whenLoaded('commune', fn() => [
                'id' => $this->commune->id,
                'name' => match($locale) {
                    'ar' => $this->commune->name_ar,
                    'en' => $this->commune->name_en,
                    default => $this->commune->name_fr,
                },
            ]),
            'is_favorited' => $this->when($request->user(), fn() =>
                $request->user()?->favorites()->where('property_id', $this->id)->exists() ?? false
            ),
        ];
    }
}
