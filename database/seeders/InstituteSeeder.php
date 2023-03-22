<?php

namespace Database\Seeders;

use App\Models\Institute;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InstituteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $institutes = ['ИТИ', 'ИНПО'];
        foreach ($institutes as $institute){
            Institute::query()->create(['title' => $institute]);
        }
    }
}
