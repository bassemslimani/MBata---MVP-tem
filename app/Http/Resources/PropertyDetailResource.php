<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyDetailResource extends JsonResource
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
            'title_fr' => $this->title_fr,
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'description_fr' => $this->description_fr,
            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,
            'address' => $this->address,
            'latitude' => $this->latitude?->toFloat(),
            'longitude' => $this->longitude?->toFloat(),
            'price_per_night_dzd' => $this->price_per_night_dzd,
            'surface_area' => $this->surface_area,
            'rooms' => $this->rooms,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'max_guests' => $this->max_guests,
            'is_active' => $this->is_active,
            'is_verified' => $this->is_verified,
            'views_count' => $this->views_count,
            'average_rating' => $this->whenLoaded('visibleReviews', fn() => round($this->visibleReviews->avg('rating') ?? 0, 1)),
            'reviews_count' => $this->whenLoaded('visibleReviews', fn() => $this->visibleReviews->count()),
            'primary_image' => $this->whenLoaded('primaryImage', fn() => PropertyImageResource::make($this->primaryImage)),
            'images' => $this->whenLoaded('images', fn() => PropertyImageResource::collection($this->images)),
            'amenities' => $this->whenLoaded('amenities', fn() => AmenityResource::collection($this->amenities)),
            'property_type' => $this->whenLoaded('propertyType', fn() => PropertyTypeResource::make($this->propertyType)),
            'property_category' => $this->whenLoaded('propertyCategory', fn() => PropertyCategoryResource::make($this->propertyCategory)),
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
                'code' => $this->commune->code,
                'name' => match($locale) {
                    'ar' => $this->commune->name_ar,
                    'en' => $this->commune->name_en,
                    default => $this->commune->name_fr,
                },
                'postal_code' => $this->commune->postal_code,
            ]),
            'owner' => $this->whenLoaded('user', fn() => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'avatar' => $this->user->avatar,
            ]),
            'is_favorited' => $this->when($request->user(), fn() =>
                $request->user()?->favorites()->where('property_id', $this->id)->exists() ?? false
            ),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
