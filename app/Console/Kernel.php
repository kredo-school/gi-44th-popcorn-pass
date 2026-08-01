<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->job(new \App\Jobs\UpdateDynamicPricing)
            ->hourly()
            ->name('update_dynamic_pricing')
            ->onOneServer();

        // Calculate user similarities for recommendations (nightly at 2 AM)
        $schedule->job(new \App\Jobs\CalculateUserSimilarities)
            ->dailyAt('02:00')
            ->name('calculate_user_similarities')
            ->onOneServer();
    }
}