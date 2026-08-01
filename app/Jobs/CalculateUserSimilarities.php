<?php

namespace App\Jobs;

use App\Services\MovieRecommendationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CalculateUserSimilarities implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $service = new MovieRecommendationService();
        $service->calculateAllUserSimilarities();
    }
}