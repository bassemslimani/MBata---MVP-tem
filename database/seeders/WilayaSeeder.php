<?php

namespace Database\Seeders;

use App\Models\Wilaya;
use Illuminate\Database\Seeder;

class WilayaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wilayas = [
            ['code' => '01', 'name_fr' => 'Adrar', 'name_ar' => 'adrar', 'name_en' => 'Adrar', 'latitude' => '27.8833', 'longitude' => '-0.2833'],
            ['code' => '02', 'name_fr' => 'Chlef', 'name_ar' => 'ach-shalif', 'name_en' => 'Chlef', 'latitude' => '36.1667', 'longitude' => '1.3333'],
            ['code' => '03', 'name_fr' => 'Laghouat', 'name_ar' => 'laghwat', 'name_en' => 'Laghouat', 'latitude' => '33.7634', 'longitude' => '2.8788'],
            ['code' => '04', 'name_fr' => 'Oum El Bouaghi', 'name_ar' => 'um al-buaghi', 'name_en' => 'Oum El Bouaghi', 'latitude' => '35.8686', 'longitude' => '7.1109'],
            ['code' => '05', 'name_fr' => 'Batna', 'name_ar' => 'batna', 'name_en' => 'Batna', 'latitude' => '35.5558', 'longitude' => '6.1745'],
            ['code' => '06', 'name_fr' => 'Béjaïa', 'name_ar' => 'bijayah', 'name_en' => 'Bejaia', 'latitude' => '36.7550', 'longitude' => '5.0586'],
            ['code' => '07', 'name_fr' => 'Biskra', 'name_ar' => 'biskra', 'name_en' => 'Biskra', 'latitude' => '34.8500', 'longitude' => '5.7250'],
            ['code' => '08', 'name_fr' => 'Béchar', 'name_ar' => 'bashar', 'name_en' => 'Bechar', 'latitude' => '31.6167', 'longitude' => '-2.2167'],
            ['code' => '09', 'name_fr' => 'Blida', 'name_ar' => 'al-bulayda', 'name_en' => 'Blida', 'latitude' => '36.4700', 'longitude' => '2.8200'],
            ['code' => '10', 'name_fr' => 'Bouira', 'name_ar' => 'al-buira', 'name_en' => 'Bouira', 'latitude' => '36.3678', 'longitude' => '3.8967'],
            ['code' => '11', 'name_fr' => 'Tamanrasset', 'name_ar' => 'tamanrasset', 'name_en' => 'Tamanrasset', 'latitude' => '22.7833', 'longitude' => '5.5167'],
            ['code' => '12', 'name_fr' => 'Tébessa', 'name_ar' => 'tabassa', 'name_en' => 'Tebessa', 'latitude' => '35.4086', 'longitude' => '8.1206'],
            ['code' => '13', 'name_fr' => 'Tlemcen', 'name_ar' => 'tilimsan', 'name_en' => 'Tlemcen', 'latitude' => '34.8814', 'longitude' => '-1.3156'],
            ['code' => '14', 'name_fr' => 'Tiaret', 'name_ar' => 'tihart', 'name_en' => 'Tiaret', 'latitude' => '35.3750', 'longitude' => '1.3167'],
            ['code' => '15', 'name_fr' => 'Tizi Ouzou', 'name_ar' => 'tizi wuzu', 'name_en' => 'Tizi Ouzou', 'latitude' => '36.7167', 'longitude' => '4.0500'],
            ['code' => '16', 'name_fr' => 'Alger', 'name_ar' => 'al-jazair', 'name_en' => 'Algiers', 'latitude' => '36.7763', 'longitude' => '3.0597'],
            ['code' => '17', 'name_fr' => 'Djelfa', 'name_ar' => 'jalifa', 'name_en' => 'Djelfa', 'latitude' => '34.6733', 'longitude' => '3.2578'],
            ['code' => '18', 'name_fr' => 'Jijel', 'name_ar' => 'jijil', 'name_en' => 'Jijel', 'latitude' => '36.8000', 'longitude' => '5.7667'],
            ['code' => '19', 'name_fr' => 'Sétif', 'name_ar' => 'satif', 'name_en' => 'Setif', 'latitude' => '36.1956', 'longitude' => '5.4144'],
            ['code' => '20', 'name_fr' => 'Saïda', 'name_ar' => 'saida', 'name_en' => 'Saida', 'latitude' => '34.8283', 'longitude' => '0.1489'],
            ['code' => '21', 'name_fr' => 'Skikda', 'name_ar' => 'sukikda', 'name_en' => 'Skikda', 'latitude' => '36.8778', 'longitude' => '6.9106'],
            ['code' => '22', 'name_fr' => 'Sidi Bel Abbès', 'name_ar' => 'sidi bil abbas', 'name_en' => 'Sidi Bel Abbes', 'latitude' => '35.1922', 'longitude' => '-0.6356'],
            ['code' => '23', 'name_fr' => 'Annaba', 'name_ar' => 'annabah', 'name_en' => 'Annaba', 'latitude' => '36.9000', 'longitude' => '7.7667'],
            ['code' => '24', 'name_fr' => 'Guelma', 'name_ar' => 'qalima', 'name_en' => 'Guelma', 'latitude' => '36.4628', 'longitude' => '7.4317'],
            ['code' => '25', 'name_fr' => 'Constantine', 'name_ar' => 'qasantina', 'name_en' => 'Constantine', 'latitude' => '36.3650', 'longitude' => '6.6147'],
            ['code' => '26', 'name_fr' => 'Médéa', 'name_ar' => 'al-madiya', 'name_en' => 'Medea', 'latitude' => '36.2667', 'longitude' => '2.7500'],
            ['code' => '27', 'name_fr' => 'Mostaganem', 'name_ar' => 'mustaghanim', 'name_en' => 'Mostaganem', 'latitude' => '35.9364', 'longitude' => '0.0900'],
            ['code' => '28', 'name_fr' => 'M\'Sila', 'name_ar' => 'al-msila', 'name_en' => 'Msila', 'latitude' => '35.7081', 'longitude' => '4.5386'],
            ['code' => '29', 'name_fr' => 'Mascara', 'name_ar' => 'maskara', 'name_en' => 'Mascara', 'latitude' => '35.3925', 'longitude' => '0.1433'],
            ['code' => '30', 'name_fr' => 'Ouargla', 'name_ar' => 'warqla', 'name_en' => 'Ouargla', 'latitude' => '31.9500', 'longitude' => '5.3167'],
            ['code' => '31', 'name_fr' => 'Oran', 'name_ar' => 'wahran', 'name_en' => 'Oran', 'latitude' => '35.6911', 'longitude' => '-0.6417'],
            ['code' => '32', 'name_fr' => 'El Bayadh', 'name_ar' => 'al-bayad', 'name_en' => 'El Bayadh', 'latitude' => '33.6833', 'longitude' => '1.0167'],
            ['code' => '33', 'name_fr' => 'Illizi', 'name_ar' => 'ilizi', 'name_en' => 'Illizi', 'latitude' => '26.4833', 'longitude' => '8.4167'],
            ['code' => '34', 'name_fr' => 'Bordj Bou Arreridj', 'name_ar' => 'burj bu arririj', 'name_en' => 'Bordj Bou Arreridj', 'latitude' => '36.0736', 'longitude' => '4.7606'],
            ['code' => '35', 'name_fr' => 'Boumerdès', 'name_ar' => 'bumardas', 'name_en' => 'Boumerdes', 'latitude' => '36.7600', 'longitude' => '3.4700'],
            ['code' => '36', 'name_fr' => 'El Tarf', 'name_ar' => 'al-tarf', 'name_en' => 'El Tarf', 'latitude' => '36.7606', 'longitude' => '8.3089'],
            ['code' => '37', 'name_fr' => 'Tindouf', 'name_ar' => 'tinduf', 'name_en' => 'Tindouf', 'latitude' => '27.7000', 'longitude' => '-8.1667'],
            ['code' => '38', 'name_fr' => 'Tissemsilt', 'name_ar' => 'tissamsilt', 'name_en' => 'Tissemsilt', 'latitude' => '35.6078', 'longitude' => '1.8117'],
            ['code' => '39', 'name_fr' => 'El Oued', 'name_ar' => 'al-wad', 'name_en' => 'El Oued', 'latitude' => '33.5167', 'longitude' => '6.8500'],
            ['code' => '40', 'name_fr' => 'Khenchela', 'name_ar' => 'khanchala', 'name_en' => 'Khenchela', 'latitude' => '35.4333', 'longitude' => '7.1500'],
            ['code' => '41', 'name_fr' => 'Souk Ahras', 'name_ar' => 'suk ahras', 'name_en' => 'Souk Ahras', 'latitude' => '36.2864', 'longitude' => '7.9517'],
            ['code' => '42', 'name_fr' => 'Tipaza', 'name_ar' => 'tipaza', 'name_en' => 'Tipaza', 'latitude' => '36.5900', 'longitude' => '2.4450'],
            ['code' => '43', 'name_fr' => 'Mila', 'name_ar' => 'mila', 'name_en' => 'Mila', 'latitude' => '36.4522', 'longitude' => '6.2644'],
            ['code' => '44', 'name_fr' => 'Aïn Defla', 'name_ar' => 'ain defla', 'name_en' => 'Ain Defla', 'latitude' => '36.2500', 'longitude' => '2.1667'],
            ['code' => '45', 'name_fr' => 'Naâma', 'name_ar' => 'naama', 'name_en' => 'Naama', 'latitude' => '33.2667', 'longitude' => '-0.3167'],
            ['code' => '46', 'name_fr' => 'Aïn Témouchent', 'name_ar' => 'ain tumushant', 'name_en' => 'Ain Temouchent', 'latitude' => '35.2983', 'longitude' => '-1.1383'],
            ['code' => '47', 'name_fr' => 'Ghardaïa', 'name_ar' => 'ghardaia', 'name_en' => 'Ghardaia', 'latitude' => '32.4833', 'longitude' => '3.6750'],
            ['code' => '48', 'name_fr' => 'Relizane', 'name_ar' => 'ghalizan', 'name_en' => 'Relizane', 'latitude' => '35.9306', 'longitude' => '0.5583'],
            ['code' => '49', 'name_fr' => 'Timimoun', 'name_ar' => 'timimun', 'name_en' => 'Timimoun', 'latitude' => '29.2583', 'longitude' => '0.2417'],
            ['code' => '50', 'name_fr' => 'Bordj Badji Mokhtar', 'name_ar' => 'burj badji mukhtar', 'name_en' => 'Bordj Badji Mokhtar', 'latitude' => '21.3333', 'longitude' => '0.9500'],
            ['code' => '51', 'name_fr' => 'Ouled Djellal', 'name_ar' => 'awlad jalal', 'name_en' => 'Ouled Djellal', 'latitude' => '34.5167', 'longitude' => '4.8000'],
            ['code' => '52', 'name_fr' => 'Béni Abbès', 'name_ar' => 'bani abbas', 'name_en' => 'Beni Abbes', 'latitude' => '30.7833', 'longitude' => '-2.6667'],
            ['code' => '53', 'name_fr' => 'In Salah', 'name_ar' => 'in salah', 'name_en' => 'In Salah', 'latitude' => '27.2500', 'longitude' => '2.4667'],
            ['code' => '54', 'name_fr' => 'In Guezzam', 'name_ar' => 'in qazzam', 'name_en' => 'In Guezzam', 'latitude' => '19.5667', 'longitude' => '5.7500'],
            ['code' => '55', 'name_fr' => 'Touggourt', 'name_ar' => 'tuqqurt', 'name_en' => 'Touggourt', 'latitude' => '33.1000', 'longitude' => '6.0667'],
            ['code' => '56', 'name_fr' => 'Djanet', 'name_ar' => 'janat', 'name_en' => 'Djanet', 'latitude' => '24.5500', 'longitude' => '9.4667'],
            ['code' => '57', 'name_fr' => 'El M\'Ghair', 'name_ar' => 'al-maghir', 'name_en' => 'El Mghair', 'latitude' => '33.9167', 'longitude' => '5.9500'],
            ['code' => '58', 'name_fr' => 'El Meniaa', 'name_ar' => 'al-muniya', 'name_en' => 'El Meniaa', 'latitude' => '30.5667', 'longitude' => '2.8833'],
        ];

        foreach ($wilayas as $wilaya) {
            Wilaya::create($wilaya);
        }
    }
}
