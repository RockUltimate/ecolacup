<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PlemenaSeeder::class,
            UserSeeder::class,
            OsobaSeeder::class,
            KunSeeder::class,
            UdalostSeeder::class,
            PrihlaskaSeeder::class,
        ]);
    }
}
