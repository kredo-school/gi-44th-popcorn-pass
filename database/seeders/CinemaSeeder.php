<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CinemaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('cinemas')->insert([
            [
                'id' => '019f1306-5446-727a-bad3-ca788c8413d2',
                'cinema_name' => 'NAGOYA',
                'city' => 'Nagoya',
                'address' => 'Nagoya, Aichi',
                'phone' => '052-000-0000',
                'email' => 'nagoya@example.com',
                'latitude' => null,
                'longitude' => null,
                'total_screens' => 5,
                'opening_time' => '09:00:00',
                'closing_time' => '23:00:00',
                'website_url' => null,
                'is_active' => true,
                'created_by_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'cinema_name' => 'OSAKA',
                'city' => 'Osaka',
                'address' => 'Osaka, Osaka',
                'phone' => '06-0000-0000',
                'email' => 'osaka@example.com',
                'latitude' => null,
                'longitude' => null,
                'total_screens' => 5,
                'opening_time' => '09:00:00',
                'closing_time' => '23:00:00',
                'website_url' => null,
                'is_active' => true,
                'created_by_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'cinema_name' => 'TOKYO',
                'city' => 'Tokyo',
                'address' => 'Shinjuku, Tokyo',
                'phone' => '03-0000-0000',
                'email' => 'tokyo@example.com',
                'latitude' => null,
                'longitude' => null,
                'total_screens' => 5,
                'opening_time' => '09:00:00',
                'closing_time' => '23:00:00',
                'website_url' => null,
                'is_active' => true,
                'created_by_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}