<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Showtime;
use App\Models\ScreenSeat;
use App\Models\ShowtimeSeat;

class ShowtimeSeatSeeder extends Seeder
{
    public function run(): void
    {
        $showtimes = Showtime::all();

        foreach ($showtimes as $showtime) {

            $screenSeats = ScreenSeat::where(
                'screen_id',
                $showtime->screen_id
            )->get();

            foreach ($screenSeats as $screenSeat) {

                ShowtimeSeat::firstOrCreate(
                    [
                        'showtime_id' => $showtime->id,
                        'screen_seat_id' => $screenSeat->id,
                    ],
                    [
                        'id' => Str::uuid(),
                        'seat_status' => 'available',
                        'price_at_showtime' => $screenSeat->price,
                    ]
                );
            }
        }
    }
}