<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyCategoryResource extends JsonResource
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
            'name' => match($locale) {
                'ar' => $this->name_ar,
                'en' => $this->name_en,
                default => $this->name_fr,
            },
            'name_fr' => $this->name_fr,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'description' => match($locale) {
                'ar' => $this->description_ar,
                'en' => $this->description_en,
                default => $this->description_fr,
            },
            'description_fr' => $this->description_fr,
            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,
            'is_active' => $this->is_active,
        ];
    }
}
