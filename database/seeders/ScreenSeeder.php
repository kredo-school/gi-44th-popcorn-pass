<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ScreenSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('screens')->insert([
            [
                'id' => (string) Str::uuid(),
                'cinema_id' => '019f1306-5446-727a-bad3-ca788c8413d2', // NAGOYA
                'screen_number' => 1,
                'screen_name' => 'Screen 1',
                'screen_type' => 'Standard',
                'layout_id' => '13743dde-1517-4603-8e4f-66dac766af1c',
                'total_seats' => 100,
                'is_active' => true,
                'created_by_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}