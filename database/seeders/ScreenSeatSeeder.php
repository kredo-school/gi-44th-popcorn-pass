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
        /*
        |--------------------------------------------------------------------------
        | Seat Category
        |--------------------------------------------------------------------------
        |
        | Current database has "Standard" only.
        |
        */
        $standard = SeatCategory::where('title', 'Standard')->first() ?? SeatCategory::where('title', 'Regular')->first();

        if (!$standard) {
            $this->command?->error(
                'SeatCategory "Standard" was not found.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | All Screens
        |--------------------------------------------------------------------------
        */
        $screens = Screen::where('is_active', true)->get();

        if ($screens->isEmpty()) {
            $this->command?->warn(
                'No active screens found.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Create Seats
        |--------------------------------------------------------------------------
        |
        | Rows A - J
        | 12 seats per row
        |
        | Total: 120 seats per screen
        |
        */
        foreach ($screens as $screen) {

            foreach (range('A', 'J') as $row) {

                for ($i = 1; $i <= 12; $i++) {

                    ScreenSeat::firstOrCreate(
                        [
                            'screen_id' => $screen->id,
                            'seat_number' => $row . $i,
                        ],
                        [
                            'id' => (string) Str::uuid(),

                            'seat_row' => $row,

                            'seat_position' => $i,

                            'seat_category_id' => $standard->id,

                            'price' => $standard->base_price ?? 1600,

                            'is_wheelchair_accessible' =>
                                $row === 'A' &&
                                in_array($i, [1, 2, 11, 12], true),

                            'is_blocked' => false,
                        ]
                    );
                }
            }

            $this->command?->info(
                "Seats ready for {$screen->screen_name}"
            );
        }

        $this->command?->info(
            'Screen seats seeded successfully.'
        );
    }
}