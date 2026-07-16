<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\GenreSeeder;
use Database\Seeders\AgeRatingSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
{
    $this->call([
        GenreSeeder::class,
        AgeRatingSeeder::class,
        AdminSeeder::class,

        CinemaSeeder::class,
        TheaterLayoutSeeder::class,

        SeatCategorySeeder::class, 

        ScreenSeeder::class,
        ScreenSeatSeeder::class,
        ShowtimeSeatSeeder::class,
    ]);
}
}
