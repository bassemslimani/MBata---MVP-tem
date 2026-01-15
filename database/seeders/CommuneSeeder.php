<?php

namespace Database\Seeders;

use App\Models\Commune;
use App\Models\Wilaya;
use Illuminate\Database\Seeder;

class CommuneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample communes for a few key wilayas
        // In production, this should include all 1541 communes
        $communes = [
            // Alger (16) - Sample communes
            ['wilaya_code' => '16', 'code' => '1601', 'name_fr' => 'Alger Centre', 'name_ar' => 'al-jazair al-markaz', 'name_en' => 'Algiers Center', 'postal_code' => '16000'],
            ['wilaya_code' => '16', 'code' => '1602', 'name_fr' => 'Sidi M\'Hamed', 'name_ar' => 'sidi muhammad', 'name_en' => 'Sidi M\'Hamed', 'postal_code' => '16000'],
            ['wilaya_code' => '16', 'code' => '1603', 'name_fr' => 'El Biar', 'name_ar' => 'al-biyar', 'name_en' => 'El Biar', 'postal_code' => '16000'],
            ['wilaya_code' => '16', 'code' => '1604', 'name_fr' => 'Bab El Oued', 'name_ar' => 'bab al-wad', 'name_en' => 'Bab El Oued', 'postal_code' => '16000'],
            ['wilaya_code' => '16', 'code' => '1605', 'name_fr' => 'Bologhine', 'name_ar' => 'bulujin', 'name_en' => 'Bologhine', 'postal_code' => '16000'],
            ['wilaya_code' => '16', 'code' => '1606', 'name_fr' => 'Hussein Dey', 'name_ar' => 'husayn day', 'name_en' => 'Hussein Dey', 'postal_code' => '16000'],
            ['wilaya_code' => '16', 'code' => '1607', 'name_fr' => 'Kouba', 'name_ar' => 'kuba', 'name_en' => 'Kouba', 'postal_code' => '16000'],
            ['wilaya_code' => '16', 'code' => '1608', 'name_fr' => 'El Mouradia', 'name_ar' => 'al-muradia', 'name_en' => 'El Mouradia', 'postal_code' => '16000'],
            ['wilaya_code' => '16', 'code' => '1609', 'name_fr' => 'Hydra', 'name_ar' => 'hadra', 'name_en' => 'Hydra', 'postal_code' => '16000'],

            // Oran (31) - Sample communes
            ['wilaya_code' => '31', 'code' => '3101', 'name_fr' => 'Oran', 'name_ar' => 'wahran', 'name_en' => 'Oran', 'postal_code' => '31000'],
            ['wilaya_code' => '31', 'code' => '3102', 'name_fr' => 'Es Senia', 'name_ar' => 'al-saniya', 'name_en' => 'Es Senia', 'postal_code' => '31000'],
            ['wilaya_code' => '31', 'code' => '3103', 'name_fr' => 'Bir El Djir', 'name_ar' => 'bir al-jir', 'name_en' => 'Bir El Djir', 'postal_code' => '31000'],

            // Constantine (25) - Sample communes
            ['wilaya_code' => '25', 'code' => '2501', 'name_fr' => 'Constantine', 'name_ar' => 'qasantina', 'name_en' => 'Constantine', 'postal_code' => '25000'],
            ['wilaya_code' => '25', 'code' => '2502', 'name_fr' => 'El Khroub', 'name_ar' => 'al-karub', 'name_en' => 'El Khroub', 'postal_code' => '25000'],
            ['wilaya_code' => '25', 'code' => '2503', 'name_fr' => 'Ain Smara', 'name_ar' => 'ain smara', 'name_en' => 'Ain Smara', 'postal_code' => '25000'],

            // Annaba (23) - Sample communes
            ['wilaya_code' => '23', 'code' => '2301', 'name_fr' => 'Annaba', 'name_ar' => 'annabah', 'name_en' => 'Annaba', 'postal_code' => '23000'],
            ['wilaya_code' => '23', 'code' => '2302', 'name_fr' => 'El Bouni', 'name_ar' => 'al-buni', 'name_en' => 'El Bouni', 'postal_code' => '23000'],
            ['wilaya_code' => '23', 'code' => '2303', 'name_fr' => 'Sidi Amar', 'name_ar' => 'sidi amar', 'name_en' => 'Sidi Amar', 'postal_code' => '23000'],

            // Batna (05) - Sample communes
            ['wilaya_code' => '05', 'code' => '0501', 'name_fr' => 'Batna', 'name_ar' => 'batna', 'name_en' => 'Batna', 'postal_code' => '05000'],
            ['wilaya_code' => '05', 'code' => '0502', 'name_fr' => 'Fesdis', 'name_ar' => 'fasdis', 'name_en' => 'Fesdis', 'postal_code' => '05000'],
            ['wilaya_code' => '05', 'code' => '0503', 'name_fr' => 'Tazoult-Lambese', 'name_ar' => 'tazult-lambisis', 'name_en' => 'Tazoult-Lambese', 'postal_code' => '05000'],

            // Setif (19) - Sample communes
            ['wilaya_code' => '19', 'code' => '1901', 'name_fr' => 'Sétif', 'name_ar' => 'satif', 'name_en' => 'Setif', 'postal_code' => '19000'],
            ['wilaya_code' => '19', 'code' => '1902', 'name_fr' => 'El Eulma', 'name_ar' => 'al-ulma', 'name_en' => 'El Eulma', 'postal_code' => '19000'],
            ['wilaya_code' => '19', 'code' => '1903', 'name_fr' => 'Ain Arnat', 'name_ar' => 'ain arnat', 'name_en' => 'Ain Arnat', 'postal_code' => '19000'],

            // Tlemcen (13) - Sample communes
            ['wilaya_code' => '13', 'code' => '1301', 'name_fr' => 'Tlemcen', 'name_ar' => 'tilimsan', 'name_en' => 'Tlemcen', 'postal_code' => '13000'],
            ['wilaya_code' => '13', 'code' => '1302', 'name_fr' => 'Chetouane', 'name_ar' => 'shatwan', 'name_en' => 'Chetouane', 'postal_code' => '13000'],
            ['wilaya_code' => '13', 'code' => '1303', 'name_fr' => 'Sebbaa Ain El Houtz', 'name_ar' => 'saba ain al-hawz', 'name_en' => 'Sebbaa Ain El Houtz', 'postal_code' => '13000'],

            // Bejaia (06) - Sample communes
            ['wilaya_code' => '06', 'code' => '0601', 'name_fr' => 'Béjaïa', 'name_ar' => 'bijayah', 'name_en' => 'Bejaia', 'postal_code' => '06000'],
            ['wilaya_code' => '06', 'code' => '0602', 'name_fr' => 'Kherrata', 'name_ar' => 'kharata', 'name_en' => 'Kherrata', 'postal_code' => '06000'],
            ['wilaya_code' => '06', 'code' => '0603', 'name_fr' => 'Aokas', 'name_ar' => 'awkas', 'name_en' => 'Aokas', 'postal_code' => '06000'],
        ];

        foreach ($communes as $commune) {
            $wilaya = Wilaya::where('code', $commune['wilaya_code'])->first();
            if ($wilaya) {
                Commune::create([
                    'wilaya_id' => $wilaya->id,
                    'code' => $commune['code'],
                    'name_fr' => $commune['name_fr'],
                    'name_ar' => $commune['name_ar'],
                    'name_en' => $commune['name_en'],
                    'postal_code' => $commune['postal_code'],
                ]);
            }
        }
    }
}
