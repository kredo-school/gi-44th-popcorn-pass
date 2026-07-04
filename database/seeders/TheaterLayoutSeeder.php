<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TheaterLayoutSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('theater_layouts')->insert([
            [
                'id' => Str::uuid(),
                'layout_name' => 'Default Layout',
                'description' => 'Dummy layout',
                'total_seats' => 100,
                'rows' => 10,
                'seats_per_row' => 10,
                'is_template' => true,
                'created_by_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}