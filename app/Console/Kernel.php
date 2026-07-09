<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)  // ← This should be here
    {
        $schedule->job(new \App\Jobs\UpdateDynamicPricing)
            ->hourly()
            ->name('update_dynamic_pricing')
            ->onOneServer();
    }
}