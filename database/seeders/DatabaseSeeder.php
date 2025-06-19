<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan; // <-- Add this line

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // User::factory(10)->create();

        $this->command->info('Seeding initial data (Categories, Languages)...');
        $this->call([
            CategorySeeder::class,
            LanguageSeeder::class,
        ]);
        $this->command->info('Initial data seeded successfully.');


        $this->command->info('Seeding book metadata...');
        $this->call(BookSeeder::class); // This must come after CategorySeeder
        $this->command->info('Book metadata seeded successfully.');

    }
}
