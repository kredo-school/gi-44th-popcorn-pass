<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Screen;
use App\Models\SeatCategory;
use App\Models\ScreenSeat;

class ScreenSeatSeeder extends Seeder
{
    public function run(): void
    {
        $regular = SeatCategory::where('title', 'Regular')->first();
        $premium = SeatCategory::where('title', 'Premium')->first();

        $screens = Screen::all();

        foreach ($screens as $screen) {

            foreach (range('A', 'J') as $row) {

                for ($i = 1; $i <= 12; $i++) {

                    $isPremium = in_array($row, ['D', 'E']);

                    ScreenSeat::firstOrCreate(
                        [
                            'screen_id' => $screen->id,
                            'seat_number' => $row . $i,
                        ],
                        [
                            'id' => Str::uuid(),
                            'seat_row' => $row,
                            'seat_position' => $i,
                            'seat_category_id' => $isPremium
                                ? $premium->id
                                : $regular->id,
                            'price' => $isPremium ? 25 : 15,
                            'is_wheelchair_accessible' => $row == 'A' && in_array($i, [1, 2, 11, 12]),
                            'is_blocked' => false,
                        ]
                    );
                }
            }
        }
    }
}