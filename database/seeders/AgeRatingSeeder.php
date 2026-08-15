<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AgeRatingSeeder extends Seeder
{
    public function run(): void
    {
        $ratings = [
    'G - General Audiences',
    'PG - Parental Guidance',
    'PG-13 - Parents Strongly Cautioned',
    'R - Restricted',
    'NC-17 - Adults Only',
];

        foreach ($ratings as $rating) {
            DB::table('age_ratings')->insertOrIgnore([
                'id' => Str::uuid(),
                'title' => $rating,
            ]);
        }
    }
}