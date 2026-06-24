<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GenreSeeder extends Seeder
{
    public function run(): void
    {
        $genres = [
            'Action',
            'Adventure',
            'Animation',
            'Comedy',
            'Crime',
            'Drama',
            'Fantasy',
            'Horror',
            'Mystery',
            'Romance',
            'Sci-Fi',
            'Thriller',
        ];

        foreach ($genres as $genre) {
            DB::table('genres')->insert([
                'id' => Str::uuid(),
                'title' => $genre,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
