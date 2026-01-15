<?php

namespace Database\Seeders;

use App\Models\PropertyType;
use Illuminate\Database\Seeder;

class PropertyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name_fr' => 'Appartement',
                'name_ar' => 'chaqa',
                'name_en' => 'Apartment',
                'icon' => 'building',
                'is_active' => true,
            ],
            [
                'name_fr' => 'Maison',
                'name_ar' => 'dar',
                'name_en' => 'House',
                'icon' => 'home',
                'is_active' => true,
            ],
            [
                'name_fr' => 'Villa',
                'name_ar' => 'fiyla',
                'name_en' => 'Villa',
                'icon' => 'house-chimney',
                'is_active' => true,
            ],
            [
                'name_fr' => 'Studio',
                'name_ar' => 'studio',
                'name_en' => 'Studio',
                'icon' => 'door-closed',
                'is_active' => true,
            ],
            [
                'name_fr' => 'Chambre',
                'name_ar' => 'ghurfa',
                'name_en' => 'Room',
                'icon' => 'bed',
                'is_active' => true,
            ],
            [
                'name_fr' => 'Suite',
                'name_ar' => 'swita',
                'name_en' => 'Suite',
                'icon' => 'star',
                'is_active' => true,
            ],
            [
                'name_fr' => 'Duplex',
                'name_ar' => 'duplex',
                'name_en' => 'Duplex',
                'icon' => 'layers',
                'is_active' => true,
            ],
            [
                'name_fr' => 'Loft',
                'name_ar' => 'loft',
                'name_en' => 'Loft',
                'icon' => 'warehouse',
                'is_active' => true,
            ],
        ];

        foreach ($types as $type) {
            PropertyType::updateOrCreate(
                ['name_fr' => $type['name_fr']],
                $type
            );
        }
    }
}
