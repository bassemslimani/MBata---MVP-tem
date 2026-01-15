<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'image_path' => $this->image_path,
            'image_url' => $this->image_path ? asset('storage/' . $this->image_path) : null,
            'is_primary' => $this->is_primary,
            'order' => $this->order,
            'alt_text' => $this->alt_text_fr,
            'alt_text_fr' => $this->alt_text_fr,
            'alt_text_ar' => $this->alt_text_ar,
            'alt_text_en' => $this->alt_text_en,
        ];
    }
}
