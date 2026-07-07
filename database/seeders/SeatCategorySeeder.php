<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\SeatCategory;

class SeatCategorySeeder extends Seeder
{
    public function run(): void
    {
        SeatCategory::create([
            'id' => Str::uuid(),
            'title' => 'Regular',
            'base_price' => 15,
            'description' => 'Regular Seat',
        ]);

        SeatCategory::create([
            'id' => Str::uuid(),
            'title' => 'Premium',
            'base_price' => 25,
            'description' => 'Premium Seat',
        ]);
    }
}