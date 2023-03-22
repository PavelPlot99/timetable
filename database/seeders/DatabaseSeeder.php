<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Validation\Rules\In;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            InstituteSeeder::class
        ]);
    }
}
