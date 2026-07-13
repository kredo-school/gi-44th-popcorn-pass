<?php

namespace Database\Seeders;

use App\Models\InformationCategory;
use Illuminate\Database\Seeder;

class InformationCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        InformationCategory::create([
            'name' => 'General',
            'color' => '#6C757D',
        ]);

        InformationCategory::create([
            'name' => 'Promotion',
            'color' => '#F5C126',
        ]);

        InformationCategory::create([
            'name' => 'Maintenance',
            'color' => '#DC3545',
        ]);

        InformationCategory::create([
            'name' => 'Event',
            'color' => '#198754',
        ]);
    }
}
