<?php

namespace App\Http\Controllers;

use App\Models\Cinema;
use Illuminate\View\View;

class CinemaController extends Controller
{
    /**
     * Display the specified cinema profile
     */
    public function show(Cinema $cinema): View
    {
        // Load cinema with reviews and user info
        $cinema->load('reviews.user');

        // Get average scores
        $avgScores = [
            'overall' => $cinema->avg_experience_score,
            'image_quality' => $cinema->avg_image_quality,
            'sound_quality' => $cinema->avg_sound_quality,
            'seat_comfort' => $cinema->avg_seat_comfort,
            'crowding_level' => $cinema->avg_crowding_level,
            'accessibility' => $cinema->avg_accessibility,
            'service_quality' => $cinema->avg_service_quality,
        ];

        // Get recent reviews
        $reviews = $cinema->reviews()
            ->with('user')
            ->latest()
            ->paginate(5);

        return view('cinemas.show', [
            'cinema' => $cinema,
            'avgScores' => $avgScores,
            'reviews' => $reviews,
        ]);
    }
}