<?php

namespace Database\Seeders;

use App\Models\Cinema;
use App\Models\TheaterLayout;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ScreenSeeder extends Seeder
{
    /**
     * Seed screens for every active cinema.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Theater Layout
        |--------------------------------------------------------------------------
        */
        $layout = TheaterLayout::first();

        if (!$layout) {
            $this->command?->warn(
                'No theater layout found. Please run TheaterLayoutSeeder first.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Active Cinemas
        |--------------------------------------------------------------------------
        */
        $cinemas = Cinema::where('is_active', true)
            ->orderBy('cinema_name')
            ->get();

        if ($cinemas->isEmpty()) {
            $this->command?->warn(
                'No active cinemas found.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Create Screens
        |--------------------------------------------------------------------------
        |
        | Each cinema gets screens according to cinemas.total_screens.
        |
        | Existing screens are NOT deleted.
        | Existing cinema_id + screen_number combinations are skipped.
        |
        */
        foreach ($cinemas as $cinema) {
            $totalScreens = max(
                1,
                (int) $cinema->total_screens
            );

            for ($screenNumber = 1; $screenNumber <= $totalScreens; $screenNumber++) {

                /*
                |--------------------------------------------------------------------------
                | Do not duplicate existing screens
                |--------------------------------------------------------------------------
                */
                $exists = DB::table('screens')
                    ->where(
                        'cinema_id',
                        $cinema->id
                    )
                    ->where(
                        'screen_number',
                        $screenNumber
                    )
                    ->exists();

                if ($exists) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Screen Type
                |--------------------------------------------------------------------------
                */
                $screenType = $this->getScreenType(
                    $screenNumber
                );

                /*
                |--------------------------------------------------------------------------
                | Seat Count
                |--------------------------------------------------------------------------
                */
                $totalSeats = $this->getSeatCount(
                    $screenType
                );

                /*
                |--------------------------------------------------------------------------
                | Insert Screen
                |--------------------------------------------------------------------------
                */
                DB::table('screens')->insert([
                    'id' => (string) Str::uuid(),

                    'cinema_id' => $cinema->id,

                    'screen_number' => $screenNumber,

                    'screen_name' =>
                        'Screen ' . $screenNumber,

                    'screen_type' => $screenType,

                    'layout_id' => $layout->id,

                    'total_seats' => $totalSeats,

                    'is_active' => true,

                    'created_by_id' => null,

                    'created_at' => now(),

                    'updated_at' => now(),
                ]);
            }

            $this->command?->info(
                "{$cinema->cinema_name}: {$totalScreens} screens ready."
            );
        }
    }

    /**
     * Determine screen type.
     */
    private function getScreenType(
        int $screenNumber
    ): string {
        return match ($screenNumber % 5) {
            3 => 'IMAX',
            4 => '4DX',
            0 => 'Dolby Cinema',
            default => 'Standard',
        };
    }

    /**
     * Determine seat count from screen type.
     */
    private function getSeatCount(
        string $screenType
    ): int {
        return match ($screenType) {
            'IMAX' => 150,
            '4DX' => 80,
            'Dolby Cinema' => 120,
            default => 100,
        };
    }
}