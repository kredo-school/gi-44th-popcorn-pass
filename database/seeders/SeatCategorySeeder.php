<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\SeatCategory;

class SeatCategorySeeder extends Seeder
{
    public function run(): void
    {
        SeatCategory::firstOrCreate(
            ['title' => 'Standard'],
            [
                'id' => Str::uuid(),
                'base_price' => 15,
                'description' => 'Standard Seat',
            ]
        );

        SeatCategory::firstOrCreate(
            ['title' => 'Regular'],
            [
                'id' => Str::uuid(),
                'base_price' => 15,
                'description' => 'Regular Seat',
            ]
        );

        SeatCategory::firstOrCreate(
            ['title' => 'Premium'],
            [
                'id' => Str::uuid(),
                'base_price' => 25,
                'description' => 'Premium Seat',
            ]
        );
    }
}