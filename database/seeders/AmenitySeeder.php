<?php

namespace Database\Seeders;

use App\Models\Amenity;
use Illuminate\Database\Seeder;

class AmenitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $amenities = [
            [
                'name_fr' => 'Eau',
                'name_ar' => 'al-ma',
                'name_en' => 'Water',
                'icon' => 'droplet',
                'is_active' => true,
            ],
            [
                'name_fr' => 'Gaz',
                'name_ar' => 'al-ghaz',
                'name_en' => 'Gas',
                'icon' => 'fire',
                'is_active' => true,
            ],
            [
                'name_fr' => 'Électricité',
                'name_ar' => 'al-kahraba',
                'name_en' => 'Electricity',
                'icon' => 'zap',
                'is_active' => true,
            ],
            [
                'name_fr' => 'WiFi',
                'name_ar' => 'wifi',
                'name_en' => 'WiFi',
                'icon' => 'wifi',
                'is_active' => true,
            ],
            [
                'name_fr' => 'Climatisation',
                'name_ar' => 'takayyut',
                'name_en' => 'Air Conditioning',
                'icon' => 'snowflake',
                'is_active' => true,
            ],
            [
                'name_fr' => 'Chauffage',
                'name_ar' => 'tadfiya',
                'name_en' => 'Heating',
                'icon' => 'flame',
                'is_active' => true,
            ],
            [
                'name_fr' => 'Parking',
                'name_ar' => 'mawqif al-sayyarat',
                'name_en' => 'Parking',
                'icon' => 'car',
                'is_active' => true,
            ],
            [
                'name_fr' => 'Ascenseur',
                'name_ar' => 'mas\'ad',
                'name_en' => 'Elevator',
                'icon' => 'arrow-up-down',
                'is_active' => true,
            ],
            [
                'name_fr' => 'Lave-linge',
                'name_ar' => 'ghassal al-malabis',
                'name_en' => 'Washing Machine',
                'icon' => 'shirt',
                'is_active' => true,
            ],
            [
                'name_fr' => 'Cuisine équipée',
                'name_ar' => 'matbakh mujahhaz',
                'name_en' => 'Equipped Kitchen',
                'icon' => 'chef-hat',
                'is_active' => true,
            ],
            [
                'name_fr' => 'Télévision',
                'name_ar' => 'tilivizyun',
                'name_en' => 'Television',
                'icon' => 'tv',
                'is_active' => true,
            ],
            [
                'name_fr' => 'Réfrigérateur',
                'name_ar' => 'thallaja',
                'name_en' => 'Refrigerator',
                'icon' => 'snowflake',
                'is_active' => true,
            ],
            [
                'name_fr' => 'Piscine',
                'name_ar' => 'al-masbaha',
                'name_en' => 'Swimming Pool',
                'icon' => 'waves',
                'is_active' => true,
            ],
            [
                'name_fr' => 'Jardin',
                'name_ar' => 'al-hadiga',
                'name_en' => 'Garden',
                'icon' => 'leaf',
                'is_active' => true,
            ],
            [
                'name_fr' => 'Sécurité 24/7',
                'name_ar' => 'amn 24/7',
                'name_en' => '24/7 Security',
                'icon' => 'shield',
                'is_active' => true,
            ],
            [
                'name_fr' => 'Balcon',
                'name_ar' => 'balcon',
                'name_en' => 'Balcony',
                'icon' => 'border-all',
                'is_active' => true,
            ],
            [
                'name_fr' => 'Terrasse',
                'name_ar' => 'saha',
                'name_en' => 'Terrace',
                'icon' => 'sun',
                'is_active' => true,
            ],
            [
                'name_fr' => 'Linge de house fourni',
                'name_ar' => 'tajhid al-sadir',
                'name_en' => 'Bed Linens Provided',
                'icon' => 'bed-double',
                'is_active' => true,
            ],
            [
                'name_fr' => 'Serviettes fourni',
                'name_ar' => 'al-futamat',
                'name_en' => 'Towels Provided',
                'icon' => 'hand',
                'is_active' => true,
            ],
            [
                'name_fr' => 'Interdiction de fumer',
                'name_ar' => 'mumanaa al-tadkhin',
                'name_en' => 'No Smoking',
                'icon' => 'ban',
                'is_active' => true,
            ],
        ];

        foreach ($amenities as $amenity) {
            Amenity::updateOrCreate(
                ['name_fr' => $amenity['name_fr']],
                $amenity
            );
        }
    }
}
