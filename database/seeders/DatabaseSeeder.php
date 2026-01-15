<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed localization data
        $this->call([
            WilayaSeeder::class,
            CommuneSeeder::class,
        ]);

        // Seed property reference data
        $this->call([
            PropertyTypeSeeder::class,
            PropertyCategorySeeder::class,
            AmenitySeeder::class,
        ]);

        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
