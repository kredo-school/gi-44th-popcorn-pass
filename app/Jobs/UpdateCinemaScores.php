<?php

namespace App\Jobs;

use App\Models\Cinema;
use App\Models\CinemaReview;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class UpdateCinemaScores implements ShouldQueue
{
    use Queueable;

    protected string $cinemaId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $cinemaId)
    {
        $this->cinemaId = $cinemaId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $cinema = Cinema::find($this->cinemaId);

        if (!$cinema) {
            return;
        }

        // Get all reviews for this cinema
        $reviews = CinemaReview::where('cinema_id', $this->cinemaId)->get();

        if ($reviews->isEmpty()) {
            // No reviews, reset to defaults
            $cinema->update([
                'avg_image_quality' => 4.0,
                'avg_sound_quality' => 4.0,
                'avg_seat_comfort' => 4.0,
                'avg_crowding_level' => 3.0,
                'avg_accessibility' => 4.0,
                'avg_service_quality' => 4.0,
                'avg_experience_score' => 4.0,
                'total_reviews' => 0,
                'last_score_update' => null,
            ]);

            return;
        }

        // Calculate averages for each dimension
        $avgImageQuality = round($reviews->avg('image_quality'), 1);
        $avgSoundQuality = round($reviews->avg('sound_quality'), 1);
        $avgSeatComfort = round($reviews->avg('seat_comfort'), 1);
        $avgCrowdingLevel = round($reviews->avg('crowding_level'), 1);
        $avgAccessibility = round($reviews->avg('accessibility'), 1);
        $avgServiceQuality = round($reviews->avg('service_quality'), 1);

        // Calculate overall experience score
        // Note: crowding_level is inverted (lower crowding = higher score)
        $overallScores = $reviews->map(function ($review) {
            $invertedCrowding = 5.0 - $review->crowding_level;
            $total = $review->image_quality
                + $review->sound_quality
                + $review->seat_comfort
                + $invertedCrowding
                + $review->accessibility
                + $review->service_quality;

            return round($total / 6, 1);
        });

        $avgExperienceScore = round($overallScores->avg(), 1);

        // Update cinema with calculated scores
        $cinema->update([
            'avg_image_quality' => $avgImageQuality,
            'avg_sound_quality' => $avgSoundQuality,
            'avg_seat_comfort' => $avgSeatComfort,
            'avg_crowding_level' => $avgCrowdingLevel,
            'avg_accessibility' => $avgAccessibility,
            'avg_service_quality' => $avgServiceQuality,
            'avg_experience_score' => $avgExperienceScore,
            'total_reviews' => $reviews->count(),
            'last_score_update' => now(),
        ]);

        // Log the update
        \Log::info('Cinema scores updated', [
            'cinema_id' => $this->cinemaId,
            'cinema_name' => $cinema->cinema_name,
            'total_reviews' => $reviews->count(),
            'avg_experience_score' => $avgExperienceScore,
        ]);
    }
}