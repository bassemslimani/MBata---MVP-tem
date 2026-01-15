<?php

namespace Database\Seeders;

use App\Models\PropertyCategory;
use Illuminate\Database\Seeder;

class PropertyCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name_fr' => 'Famille',
                'name_ar' => 'usra',
                'name_en' => 'Family',
                'description_fr' => 'Logements idéaux pour les familles avec enfants.',
                'description_ar' => 'maskunat Mumtaida li-l-usrat bi-atfal.',
                'description_en' => 'Accommodations ideal for families with children.',
                'is_active' => true,
            ],
            [
                'name_fr' => 'Voyage d\'affaires',
                'name_ar' => 'safar tijaara',
                'name_en' => 'Business Travel',
                'description_fr' => 'Logements adaptés aux professionnels en déplacement.',
                'description_ar' => 'maskunat mutanasiya ma-a al-muhtaribin.',
                'description_en' => 'Accommodations suitable for business travelers.',
                'is_active' => true,
            ],
            [
                'name_fr' => 'Vacances',
                'name_ar' => 'utla',
                'name_en' => 'Vacation',
                'description_fr' => 'Parfait pour les vacances et les séjours de détente.',
                'description_ar' => 'kamil li-l-utla wa-uqaf al-istiraha.',
                'description_en' => 'Perfect for holidays and relaxing stays.',
                'is_active' => true,
            ],
            [
                'name_fr' => 'Court séjour',
                'name_ar' => 'uqaf qasira',
                'name_en' => 'Short Stay',
                'description_fr' => 'Pour les séjours de courte durée (1-3 nuits).',
                'description_ar' => 'li-l-uqaf al-mudda qasira (1-3 layal).',
                'description_en' => 'For short stays (1-3 nights).',
                'is_active' => true,
            ],
            [
                'name_fr' => 'Long séjour',
                'name_ar' => 'uqaf tawila',
                'name_en' => 'Long Stay',
                'description_fr' => 'Pour les séjours de longue durée avec tarifs dégressifs.',
                'description_ar' => 'li-l-uqaf al-mudda tawila bi-asaar munkhafida.',
                'description_en' => 'For long stays with discounted rates.',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            PropertyCategory::updateOrCreate(
                ['name_fr' => $category['name_fr']],
                $category
            );
        }
    }
}
