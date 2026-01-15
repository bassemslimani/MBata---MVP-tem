<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyTypeResource extends JsonResource
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
            'icon' => $this->icon,
            'is_active' => $this->is_active,
        ];
    }
}
